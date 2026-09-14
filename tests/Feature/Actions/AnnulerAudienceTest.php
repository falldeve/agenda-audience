<?php

use App\Actions\AnnulerAudience;
use App\Livewire\Secretaire\Agenda;
use App\Livewire\Secretaire\FicheAudience;
use App\Models\Audience;
use App\Models\User;
use Livewire\Livewire;

function audienceProgrammee(User $secretaire, string $nom = 'Moussa Ba'): Audience
{
    return Audience::create([
        'demandeur_nom' => $nom,
        'objet' => 'Tarification',
        'recue_le' => '2026-09-01',
        'creneau' => now()->startOfWeek()->addDays(2)->setTime(11, 0),
        'statut' => 'programmee',
        'saisie_par' => $secretaire->id,
    ]);
}

test('annuler passe le statut à annulee, libère le créneau et trace l\'événement', function () {
    $secretaire = User::factory()->create();
    $this->actingAs($secretaire);

    $audience = audienceProgrammee($secretaire);

    app(AnnulerAudience::class)->execute($audience, 'Demandeur indisponible.');

    $audience->refresh();

    expect($audience->statut)->toBe('annulee');
    expect($audience->creneau)->toBeNull();

    $evenement = $audience->evenements()->where('type', 'annulation')->sole();

    expect($evenement->libelle)->toBe('Demande annulée');
    expect($evenement->detail)->toBe('Demandeur indisponible.');
    expect($evenement->auteur_id)->toBe($secretaire->id);
});

test('le motif d\'annulation est facultatif', function () {
    $secretaire = User::factory()->create();
    $this->actingAs($secretaire);

    $audience = audienceProgrammee($secretaire);

    app(AnnulerAudience::class)->execute($audience);

    expect($audience->evenements()->where('type', 'annulation')->sole()->detail)->toBeNull();
});

test('une audience annulée disparaît de la grille et de la liste à placer', function () {
    $secretaire = User::factory()->create();
    $this->actingAs($secretaire);

    $programmee = audienceProgrammee($secretaire, 'Moussa Ba');

    $aPlacer = Audience::create([
        'demandeur_nom' => 'Ibrahima Sow',
        'objet' => 'Coordination régionale',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => $secretaire->id,
    ]);

    Livewire::test(Agenda::class)
        ->assertSee('Moussa Ba')
        ->assertSee('Ibrahima Sow');

    app(AnnulerAudience::class)->execute($programmee);
    app(AnnulerAudience::class)->execute($aPlacer);

    Livewire::test(Agenda::class)
        ->assertDontSee('Moussa Ba')
        ->assertDontSee('Ibrahima Sow');

    expect(Audience::aPlacer()->count())->toBe(0);
});

test('la fiche annule depuis la modale puis masque les actions', function () {
    $secretaire = User::factory()->create();
    $audience = audienceProgrammee($secretaire);

    Livewire::actingAs($secretaire)
        ->test(FicheAudience::class, ['audience' => $audience])
        ->assertSee('Annuler la demande')
        ->call('ouvrirAnnulation')
        ->assertSet('confirmationAnnulation', true)
        ->set('motifAnnulation', 'Reporté à la demande du cabinet.')
        ->call('confirmerAnnulation')
        ->assertSet('confirmationAnnulation', false)
        ->assertSee('Annulée')
        ->assertSee('Demande annulée')
        ->assertDontSee('Annuler la demande')
        ->assertDontSee('Modifier');

    expect($audience->refresh()->statut)->toBe('annulee');
});
