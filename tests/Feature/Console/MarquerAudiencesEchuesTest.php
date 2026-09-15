<?php

use App\Livewire\Directeur\Decisions;
use App\Models\Audience;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    // Les créneaux de ce fichier sont datés de septembre 2026 : l'horloge est figée en aval
    // pour que le 15/09 soit passé et le 18/09 encore à venir, quelle que soit la date réelle.
    Carbon::setTestNow('2026-09-16 12:00:00');

    $this->secretaire = User::factory()->create(['role' => 'secretaire']);
});

function audience(string $nom, string $statut, ?string $creneau): Audience
{
    return Audience::create([
        'demandeur_nom' => $nom,
        'objet' => 'Objet de démonstration',
        'recue_le' => '2026-09-01',
        'statut' => $statut,
        'creneau' => $creneau,
        'saisie_par' => test()->secretaire->id,
    ]);
}

test('bascule en échue les audiences validées dont le créneau est passé', function () {
    $echue = audience('Amadou Diop', 'validee', '2026-09-15 10:00:00');

    $this->artisan('audiences:marquer-echues')->assertSuccessful();

    expect($echue->refresh()->statut)->toBe('echue');

    $evenement = $echue->evenements()->where('type', 'echeance')->sole();

    expect($evenement->libelle)->toBe('Créneau échu');
    expect($evenement->detail)->toBe('Créneau du 15/09/2026 à 10h00');
    expect($evenement->auteur_id)->toBeNull();
});

test('laisse intactes les audiences validées encore à venir', function () {
    $aVenir = audience('Mariama Fall', 'validee', '2026-09-18 10:00:00');

    $this->artisan('audiences:marquer-echues')->assertSuccessful();

    expect($aVenir->refresh()->statut)->toBe('validee');
    expect($aVenir->evenements()->where('type', 'echeance')->exists())->toBeFalse();
});

test('laisse intactes les audiences passées qui ne sont pas validées', function (string $statut) {
    $passee = audience('Moussa Ba', $statut, '2026-09-15 10:00:00');

    $this->artisan('audiences:marquer-echues')->assertSuccessful();

    expect($passee->refresh()->statut)->toBe($statut);
    expect($passee->evenements()->where('type', 'echeance')->exists())->toBeFalse();
})->with([
    'en attente du directeur' => 'programmee',
    'refusée' => 'refusee',
    'annulée' => 'annulee',
]);

test('peut être relancée sans créer de doublon', function () {
    $echue = audience('Amadou Diop', 'validee', '2026-09-15 10:00:00');

    $this->artisan('audiences:marquer-echues')->assertSuccessful();
    $this->artisan('audiences:marquer-echues')->assertSuccessful();

    expect($echue->refresh()->statut)->toBe('echue');
    expect($echue->evenements()->where('type', 'echeance')->count())->toBe(1);
});

test("la commande n'affirme jamais qu'une audience s'est tenue", function () {
    $echue = audience('Amadou Diop', 'validee', '2026-09-15 10:00:00');

    $this->artisan('audiences:marquer-echues')->assertSuccessful();

    expect($echue->refresh()->statut)->not->toBe('tenue');
    expect($echue->evenements()->where('type', 'tenue')->exists())->toBeFalse();
});

test('aucune audience échue, tenue ou non honorée ne remonte dans les décisions à valider', function (string $statut) {
    audience('Amadou Diop', $statut, '2026-09-15 10:00:00');
    audience('Seynabou Diop', 'programmee', '2026-09-18 14:00:00');

    Livewire::actingAs(User::factory()->create(['role' => 'directeur']))
        ->test(Decisions::class)
        ->assertDontSee('Amadou Diop')
        ->assertSee('Seynabou Diop');
})->with(['echue', 'tenue', 'non_honoree']);
