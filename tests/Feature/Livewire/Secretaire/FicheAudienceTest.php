<?php

use App\Livewire\Secretaire\FicheAudience;
use App\Models\Audience;
use App\Models\User;
use Livewire\Livewire;

test('la fiche affiche le dossier, le demandeur et son historique', function () {
    $secretaire = User::factory()->create(['name' => 'Fatou Ndiaye', 'role' => 'secretaire']);

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
    $secretaire = User::factory()->create(['role' => 'secretaire']);

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

/**
 * Les suites d'une audience échue : le marqueur retenu est l'attribut wire:click des boutons,
 * seul fragment qui ne peut pas se confondre avec une pastille de statut ou une ligne d'historique.
 */
function ficheEchue(string $statut = 'echue'): Audience
{
    return Audience::create([
        'demandeur_nom' => 'Amadou Diop',
        'objet' => 'Objet de démonstration',
        'recue_le' => '2026-09-01',
        'statut' => $statut,
        'creneau' => '2026-09-15 10:00:00',
        'saisie_par' => User::factory()->create(['role' => 'secretaire'])->id,
    ]);
}

test('la secrétaire peut préciser la suite d\'une audience échue', function () {
    $audience = ficheEchue();

    $this->actingAs(User::factory()->create(['role' => 'secretaire']))
        ->get(route('audience.fiche', $audience))
        ->assertOk()
        ->assertSee('Échue')
        ->assertSee('wire:click="confirmerTenue"', escape: false)
        ->assertSee('wire:click="ouvrirNonHonoree"', escape: false)
        ->assertDontSee('wire:click="ouvrirAnnulation"', escape: false);
});

test('les boutons de suite n\'apparaissent que sur une audience échue', function (string $statut) {
    $audience = ficheEchue($statut);

    $this->actingAs(User::factory()->create(['role' => 'secretaire']))
        ->get(route('audience.fiche', $audience))
        ->assertOk()
        ->assertDontSee('wire:click="confirmerTenue"', escape: false)
        ->assertDontSee('wire:click="ouvrirNonHonoree"', escape: false);
})->with(['programmee', 'validee', 'tenue', 'non_honoree', 'annulee']);

test('le directeur consulte une audience échue sans pouvoir en préciser la suite', function () {
    $audience = ficheEchue();

    $this->actingAs(User::factory()->create(['role' => 'directeur']))
        ->get(route('audience.fiche', $audience))
        ->assertOk()
        ->assertSee('Échue')
        ->assertDontSee('wire:click="confirmerTenue"', escape: false)
        ->assertDontSee('wire:click="ouvrirNonHonoree"', escape: false);
});

test('la secrétaire confirme la tenue depuis la fiche', function () {
    $audience = ficheEchue();

    Livewire::actingAs(User::factory()->create(['role' => 'secretaire']))
        ->test(FicheAudience::class, ['audience' => $audience])
        ->call('confirmerTenue')
        ->assertDontSeeHtml('wire:click="confirmerTenue"');

    expect($audience->refresh()->statut)->toBe('tenue');
});

test('la secrétaire constate une non-présentation avec son motif', function () {
    $audience = ficheEchue();

    Livewire::actingAs(User::factory()->create(['role' => 'secretaire']))
        ->test(FicheAudience::class, ['audience' => $audience])
        ->call('ouvrirNonHonoree')
        ->assertSet('confirmationNonHonoree', true)
        ->set('motifNonHonoree', 'Demandeur absent.')
        ->call('confirmerNonHonoree')
        ->assertSet('confirmationNonHonoree', false);

    expect($audience->refresh()->statut)->toBe('non_honoree');
    expect($audience->evenements()->where('type', 'non_honoree')->sole()->detail)->toBe('Demandeur absent.');
});

test('le directeur ne peut pas déclencher la suite par un appel direct', function () {
    $audience = ficheEchue();

    Livewire::actingAs(User::factory()->create(['role' => 'directeur']))
        ->test(FicheAudience::class, ['audience' => $audience])
        ->call('confirmerTenue')
        ->assertForbidden();

    expect($audience->refresh()->statut)->toBe('echue');
});

test('une audience déjà tranchée ne peut plus être retranchée', function () {
    $audience = ficheEchue('tenue');

    Livewire::actingAs(User::factory()->create(['role' => 'secretaire']))
        ->test(FicheAudience::class, ['audience' => $audience])
        ->call('confirmerNonHonoree')
        ->assertForbidden();

    expect($audience->refresh()->statut)->toBe('tenue');
});
