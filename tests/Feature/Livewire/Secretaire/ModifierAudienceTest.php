<?php

use App\Livewire\Secretaire\ModifierAudience;
use App\Models\Audience;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function audienceAModifier(User $secretaire): Audience
{
    return Audience::create([
        'demandeur_nom' => 'Ousmane Fall',
        'demandeur_organisation' => 'Chambre de commerce',
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'lettre_scannee' => 'lettres/courrier-initial.pdf',
        'saisie_par' => $secretaire->id,
    ]);
}

test('le formulaire est pré-rempli avec les valeurs de l\'audience', function () {
    $secretaire = User::factory()->create();
    $audience = audienceAModifier($secretaire);

    Livewire::actingAs($secretaire)
        ->test(ModifierAudience::class, ['audience' => $audience])
        ->assertSet('nom', 'Ousmane Fall')
        ->assertSet('organisation', 'Chambre de commerce')
        ->assertSet('objet', 'Demande de financement')
        ->assertSet('recueLe', '2026-09-01');
});

test('enregistrer met à jour les champs et trace une modification', function () {
    $secretaire = User::factory()->create();
    $audience = audienceAModifier($secretaire);

    Livewire::actingAs($secretaire)
        ->test(ModifierAudience::class, ['audience' => $audience])
        ->set('nom', 'Ousmane Fall Diop')
        ->set('fonction', 'Président')
        ->set('objet', 'Financement régional')
        ->set('motif', 'Dossier complété.')
        ->call('enregistrer')
        ->assertHasNoErrors()
        ->assertRedirect(route('audience.fiche', $audience));

    $audience->refresh();

    expect($audience->demandeur_nom)->toBe('Ousmane Fall Diop');
    expect($audience->demandeur_fonction)->toBe('Président');
    expect($audience->objet)->toBe('Financement régional');
    expect($audience->motif)->toBe('Dossier complété.');

    $evenement = $audience->evenements()->where('type', 'modification')->sole();

    expect($evenement->libelle)->toBe('Demande modifiée');
    expect($evenement->auteur_id)->toBe($secretaire->id);
});

test('sans nouveau fichier la lettre existante est conservée', function () {
    Storage::fake('public');
    $secretaire = User::factory()->create();
    $audience = audienceAModifier($secretaire);

    Livewire::actingAs($secretaire)
        ->test(ModifierAudience::class, ['audience' => $audience])
        ->set('objet', 'Financement régional')
        ->call('enregistrer')
        ->assertHasNoErrors();

    expect($audience->refresh()->lettre_scannee)->toBe('lettres/courrier-initial.pdf');
});

test('une nouvelle lettre remplace le chemin stocké', function () {
    Storage::fake('public');
    $secretaire = User::factory()->create();
    $audience = audienceAModifier($secretaire);

    Livewire::actingAs($secretaire)
        ->test(ModifierAudience::class, ['audience' => $audience])
        ->set('lettre', UploadedFile::fake()->create('nouvelle.pdf', 80, 'application/pdf'))
        ->call('$refresh')
        ->call('enregistrer')
        ->assertHasNoErrors();

    $audience->refresh();

    expect($audience->lettre_scannee)->not->toBe('lettres/courrier-initial.pdf');
    Storage::disk('public')->assertExists($audience->lettre_scannee);
});

test('refuse une modification qui vide le nom', function () {
    $secretaire = User::factory()->create();
    $audience = audienceAModifier($secretaire);

    Livewire::actingAs($secretaire)
        ->test(ModifierAudience::class, ['audience' => $audience])
        ->set('nom', '')
        ->call('enregistrer')
        ->assertHasErrors(['nom' => 'required'])
        ->assertNoRedirect();

    expect($audience->refresh()->demandeur_nom)->toBe('Ousmane Fall');
});
