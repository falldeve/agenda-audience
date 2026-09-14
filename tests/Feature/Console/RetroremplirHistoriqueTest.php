<?php

use App\Models\Audience;
use App\Models\User;

function audienceSansHistorique(User $secretaire, string $nom = 'Ousmane Fall'): Audience
{
    return Audience::create([
        'demandeur_nom' => $nom,
        'objet' => 'Demande de financement',
        'recue_le' => '2026-09-01',
        'statut' => 'en_attente',
        'saisie_par' => $secretaire->id,
    ]);
}

test('crée l\'événement de saisie manquant en le datant du courrier', function () {
    $secretaire = User::factory()->create();
    $audience = audienceSansHistorique($secretaire);

    $this->artisan('audiences:retroremplir-historique')->assertSuccessful();

    $evenement = $audience->evenements()->sole();

    expect($evenement->type)->toBe('saisie');
    expect($evenement->libelle)->toBe('Saisie de la demande');
    expect($evenement->auteur_id)->toBe($secretaire->id);
    expect($evenement->created_at->toDateString())->toBe('2026-09-01');
});

test('laisse intactes les audiences qui ont déjà un historique', function () {
    $secretaire = User::factory()->create();
    $dejaHistorisee = audienceSansHistorique($secretaire, 'Amadou Diop');

    $dejaHistorisee->evenements()->create([
        'type' => 'programmation',
        'libelle' => 'Audience programmée',
        'auteur_id' => $secretaire->id,
    ]);

    $this->artisan('audiences:retroremplir-historique')->assertSuccessful();

    expect($dejaHistorisee->evenements()->pluck('type')->all())->toBe(['programmation']);
});

test('peut être relancée sans créer de doublon', function () {
    $secretaire = User::factory()->create();
    $audience = audienceSansHistorique($secretaire);

    $this->artisan('audiences:retroremplir-historique')->assertSuccessful();
    $this->artisan('audiences:retroremplir-historique')->assertSuccessful();

    expect($audience->evenements()->count())->toBe(1);
});
