<?php

use App\Livewire\Secretaire\Agenda;
use App\Models\Audience;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    // Horloge figée : les créneaux de septembre 2026 doivent rester futurs
    Carbon::setTestNow('2026-09-01 08:00:00');
});

function demandeAPlacer(User $secretaire, string $nom = 'Ousmane Fall'): Audience
{
    return Audience::create([
        'demandeur_nom' => $nom,
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => $secretaire->id,
    ]);
}

test('programme une audience depuis la modale et la referme', function () {
    $secretaire = User::factory()->create();
    $aPlacer = demandeAPlacer($secretaire);

    Livewire::actingAs($secretaire)
        ->test(Agenda::class)
        ->call('ouvrirProgrammation', $aPlacer->id)
        ->assertSet('audienceAProgrammer', $aPlacer->id)
        ->set('creneauChoisi', '2026-09-08T09:00')
        ->call('confirmerProgrammation')
        ->assertHasNoErrors()
        ->assertSet('audienceAProgrammer', null);

    $aPlacer->refresh();

    expect($aPlacer->statut)->toBe('programmee');
    expect($aPlacer->creneau->toDateTimeString())->toBe('2026-09-08 09:00:00');
});

test('garde la modale ouverte avec le message du conflit quand le créneau est occupé', function () {
    // $secretaire = User::factory()->create();
    $secretaire = User::factory()->create(['role' => 'secretaire']);

    Audience::create([
        'demandeur_nom' => 'Amadou Diop',
        'objet' => 'Présentation budgétaire',
        'recue_le' => '2026-09-01',
        'statut' => 'validee',
        'creneau' => '2026-09-08 10:00:00',
        'saisie_par' => $secretaire->id,
    ]);

    $aPlacer = demandeAPlacer($secretaire);

    Livewire::actingAs($secretaire)
        ->test(Agenda::class)
        ->call('ouvrirProgrammation', $aPlacer->id)
        ->set('creneauChoisi', '2026-09-08T10:00')
        ->call('confirmerProgrammation')
        ->assertHasErrors('creneauChoisi')
        ->assertSee('Ce créneau est déjà occupé par une autre audience.')
        ->assertSet('audienceAProgrammer', $aPlacer->id);

    $aPlacer->refresh();

    expect($aPlacer->creneau)->toBeNull();
    expect($aPlacer->statut)->toBe('en_attente');
});

test('remonte dans la modale le message des règles horaires', function () {
    $secretaire = User::factory()->create();
    $aPlacer = demandeAPlacer($secretaire);

    Livewire::actingAs($secretaire)
        ->test(Agenda::class)
        ->call('ouvrirProgrammation', $aPlacer->id)
        ->set('creneauChoisi', '2026-09-12T10:00')
        ->call('confirmerProgrammation')
        ->assertSee('Les audiences se tiennent du lundi au vendredi.')
        ->assertSet('audienceAProgrammer', $aPlacer->id);

    expect($aPlacer->refresh()->creneau)->toBeNull();
});
