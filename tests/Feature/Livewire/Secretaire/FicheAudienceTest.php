<?php

use App\Models\Audience;
use App\Models\User;

test('la fiche affiche le dossier, le demandeur et son historique', function () {
    $secretaire = User::factory()->create(['name' => 'Fatou Ndiaye']);

    $audience = Audience::create([
        'demandeur_nom' => 'Ousmane Fall',
        'demandeur_organisation' => 'Chambre de commerce',
        'demandeur_fonction' => 'Président',
        'objet' => 'Demande de financement',
        'motif' => 'Présentation du dossier régional.',
        'recue_le' => '2026-09-01',
        'creneau' => '2026-09-08 09:00:00',
        'statut' => 'programmee',
        'lettre_scannee' => 'lettres/courrier-fall.pdf',
        'saisie_par' => $secretaire->id,
    ]);

    $audience->evenements()->create([
        'type' => 'saisie',
        'libelle' => 'Saisie de la demande',
        'detail' => 'Courrier enregistré par Fatou Ndiaye',
        'auteur_id' => $secretaire->id,
    ]);

    $this->actingAs($secretaire)
        ->get(route('audience.fiche', $audience))
        ->assertOk()
        ->assertSee($audience->numeroDossier())
        ->assertSee('Demande de financement')
        ->assertSee('Ousmane Fall')
        ->assertSee('Président')
        ->assertSee('Présentation du dossier régional.')
        ->assertSee('08/09/2026 à 09h00')
        ->assertSee('Saisie de la demande')
        ->assertSee('Courrier enregistré par Fatou Ndiaye')
        ->assertSee('Ouvrir le PDF')
        ->assertSee(asset('storage/lettres/courrier-fall.pdf'))
        ->assertSee('courrier-fall.pdf');
});

test('la fiche signale une audience sans créneau, sans motif et sans lettre', function () {
    $secretaire = User::factory()->create();

    $audience = Audience::create([
        'demandeur_nom' => 'Ibrahima Sow',
        'objet' => 'Coordination régionale',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => $secretaire->id,
    ]);

    $this->actingAs($secretaire)
        ->get(route('audience.fiche', $audience))
        ->assertOk()
        ->assertSee('Non encore programmé')
        ->assertSee('Aucun motif renseigné')
        ->assertSee('Aucune lettre jointe')
        ->assertSee('Aucun événement enregistré.');
});

test('la fiche est réservée aux utilisateurs connectés', function () {
    $audience = Audience::create([
        'demandeur_nom' => 'Ibrahima Sow',
        'objet' => 'Coordination régionale',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    $this->get(route('audience.fiche', $audience))->assertRedirect(route('login'));
});
