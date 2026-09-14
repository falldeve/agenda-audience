<?php

use App\Livewire\Secretaire\NouvelleDemande;
use App\Models\Audience;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('enregistre une demande valide en attente, sans créneau, avec sa lettre scannée', function () {
    Storage::fake('public');
    $secretaire = User::factory()->create();

    Livewire::actingAs($secretaire)
        ->test(NouvelleDemande::class)
        ->set('nom', 'Ousmane Fall')
        ->set('organisation', 'Chambre de commerce')
        ->set('telephone', '77 123 45 67')
        ->set('email', 'ousmane.fall@example.sn')
        ->set('objet', 'Demande de financement')
        ->set('motif', 'Présentation du dossier de financement régional.')
        ->set('recueLe', '2026-09-10')
        ->set('lettre', UploadedFile::fake()->create('lettre.pdf', 120, 'application/pdf'))
        ->call('$refresh')
        ->call('enregistrer')
        ->assertHasNoErrors()
        ->assertRedirect(route('agenda'));

    $audience = Audience::sole();

    expect($audience->demandeur_nom)->toBe('Ousmane Fall');
    expect($audience->demandeur_organisation)->toBe('Chambre de commerce');
    expect($audience->objet)->toBe('Demande de financement');
    expect($audience->recue_le->toDateString())->toBe('2026-09-10');
    expect($audience->statut)->toBe('en_attente');
    expect($audience->creneau)->toBeNull();
    expect($audience->saisie_par)->toBe($secretaire->id);

    Storage::disk('public')->assertExists($audience->lettre_scannee);
});

test('refuse une demande sans nom et n\'enregistre rien', function () {
    Storage::fake('public');

    Livewire::actingAs(User::factory()->create())
        ->test(NouvelleDemande::class)
        ->set('nom', '')
        ->set('objet', 'Demande de financement')
        ->set('recueLe', '2026-09-10')
        ->call('enregistrer')
        ->assertHasErrors(['nom' => 'required'])
        ->assertNoRedirect();

    expect(Audience::count())->toBe(0);
});
