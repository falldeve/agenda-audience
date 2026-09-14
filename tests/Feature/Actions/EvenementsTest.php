<?php

use App\Actions\EnregistrerDemande;
use App\Actions\ProgrammerAudience;
use App\Models\Audience;
use App\Models\User;

test('enregistrer une demande crée un événement de saisie lié à son auteur', function () {
    $secretaire = User::factory()->create(['name' => 'Fatou Ndiaye']);
    $this->actingAs($secretaire);

    $audience = app(EnregistrerDemande::class)->execute([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
    ]);

    $evenement = $audience->evenements()->sole();

    expect($evenement->type)->toBe('saisie');
    expect($evenement->libelle)->toBe('Saisie de la demande');
    expect($evenement->detail)->toBe('Courrier enregistré par Fatou Ndiaye');
    expect($evenement->auteur_id)->toBe($secretaire->id);
    expect($evenement->audience_id)->toBe($audience->id);
});

test('programmer une audience ajoute un événement de programmation avec le créneau', function () {
    $secretaire = User::factory()->create();
    $this->actingAs($secretaire);

    $audience = Audience::create([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => $secretaire->id,
    ]);

    app(ProgrammerAudience::class)->execute($audience, '2026-09-08T09:00');

    $evenement = $audience->evenements()->where('type', 'programmation')->sole();

    expect($evenement->libelle)->toBe('Audience programmée');
    expect($evenement->detail)->toBe('Créneau fixé au 08/09/2026 à 09:00');
    expect($evenement->auteur_id)->toBe($secretaire->id);
});

test('les événements se lisent dans l\'ordre chronologique', function () {
    $secretaire = User::factory()->create();
    $this->actingAs($secretaire);

    $audience = app(EnregistrerDemande::class)->execute([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
    ]);

    app(ProgrammerAudience::class)->execute($audience, '2026-09-08T09:00');

    expect($audience->evenements->pluck('type')->all())->toBe(['saisie', 'programmation']);
});

test('supprimer une audience supprime son historique', function () {
    $secretaire = User::factory()->create();
    $this->actingAs($secretaire);

    $audience = app(EnregistrerDemande::class)->execute([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
    ]);

    $audience->delete();

    $this->assertDatabaseEmpty('evenements');
});

test('le numéro de dossier combine l\'année et l\'identifiant complété à quatre chiffres', function () {
    $this->travelTo('2026-05-04 10:00:00');

    $audience = Audience::create([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    expect($audience->numeroDossier())->toBe('AUD-2026-0001');
});

test('le numéro de dossier reste celui de l\'année de création une fois l\'année passée', function () {
    $this->travelTo('2026-05-04 10:00:00');

    $audience = Audience::create([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    $this->travelTo('2028-02-17 08:30:00');

    expect($audience->fresh()->numeroDossier())->toBe('AUD-2026-0001');
});
