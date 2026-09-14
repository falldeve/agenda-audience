<?php

use App\Actions\ProgrammerAudience;
use App\Models\Audience;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    // Les créneaux de ce fichier sont datés de septembre 2026 : l'horloge est figée en amont
    // pour qu'ils restent futurs, sinon la règle « pas de programmation dans le passé »
    // masquerait les règles que ces tests ciblent.
    Carbon::setTestNow('2026-09-01 08:00:00');
});

test('refuse de programmer une audience sur un créneau déjà occupé, même envoyé au format datetime-local', function () {
    $secretaire = User::factory()->create();

    $occupee = Audience::create([
        'demandeur_nom' => 'Amadou Diop',
        'objet' => 'Différend foncier',
        'recue_le' => now()->toDateString(),
        'statut' => 'validee',
        'creneau' => '2026-09-08 09:00:00',
        'saisie_par' => $secretaire->id,
    ]);

    $aPlacer = Audience::create([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Litige commercial',
        'recue_le' => now()->toDateString(),
        'statut' => 'en_attente',
        'saisie_par' => $secretaire->id,
    ]);

    try {
        app(ProgrammerAudience::class)->execute($aPlacer, '2026-09-08T09:00');

        $this->fail('ValidationException attendue mais non levée.');
    } catch (ValidationException $e) {
        expect($e->errors()['creneau'])->toContain('Ce créneau est déjà occupé par une autre audience.');
    }

    expect($aPlacer->refresh())
        ->creneau->toBeNull()
        ->statut->toBe('en_attente');

    $occupee->refresh();

    expect($occupee->creneau->toDateTimeString())->toBe('2026-09-08 09:00:00');
    expect($occupee->statut)->toBe('validee');
});

test('refuse un créneau tombant le week-end', function (string $creneau) {
    $aPlacer = Audience::create([
        'demandeur_nom' => 'Aïssatou Ndiaye',
        'objet' => 'Présentation projet',
        'recue_le' => now()->toDateString(),
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    try {
        app(ProgrammerAudience::class)->execute($aPlacer, $creneau);

        $this->fail('ValidationException attendue mais non levée.');
    } catch (ValidationException $e) {
        expect($e->errors()['creneau'])->toContain('Les audiences se tiennent du lundi au vendredi.');
    }

    expect($aPlacer->refresh())
        ->creneau->toBeNull()
        ->statut->toBe('en_attente');
})->with([
    'samedi' => '2026-09-12T10:00',
    'dimanche' => '2026-09-13T10:00',
]);

test('refuse un créneau hors de la plage 9h-16h', function (string $creneau) {
    $aPlacer = Audience::create([
        'demandeur_nom' => 'Ibrahima Sow',
        'objet' => 'Coordination régionale',
        'recue_le' => now()->toDateString(),
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    try {
        app(ProgrammerAudience::class)->execute($aPlacer, $creneau);

        $this->fail('ValidationException attendue mais non levée.');
    } catch (ValidationException $e) {
        expect($e->errors()['creneau'])->toContain('Les audiences se tiennent entre 9h et 16h.');
    }

    expect($aPlacer->refresh())
        ->creneau->toBeNull()
        ->statut->toBe('en_attente');
})->with([
    '4h du matin' => '2026-09-08T04:00',
    '17h, juste après la plage' => '2026-09-08T17:00',
]);

test('programme une audience sur un créneau libre en semaine', function (string $creneau, string $creneauAttendu) {
    $aPlacer = Audience::create([
        'demandeur_nom' => 'Mariama Fall',
        'objet' => 'Convention femmes',
        'recue_le' => now()->toDateString(),
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    $programmee = app(ProgrammerAudience::class)->execute($aPlacer, $creneau);

    expect($programmee->creneau->toDateTimeString())->toBe($creneauAttendu);
    expect($programmee->statut)->toBe('programmee');

    $this->assertDatabaseHas('audiences', [
        'id' => $aPlacer->id,
        'creneau' => $creneauAttendu,
        'statut' => 'programmee',
    ]);
})->with([
    '9h, borne basse' => ['2026-09-08T09:00', '2026-09-08 09:00:00'],
    '16h, borne haute' => ['2026-09-08T16:00', '2026-09-08 16:00:00'],
]);

test('refuse un créneau situé dans le passé', function (string $creneau) {
    $aPlacer = Audience::create([
        'demandeur_nom' => 'Ousmane Fall',
        'objet' => 'Demande de financement',
        'recue_le' => now()->subMonth()->toDateString(),
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    try {
        app(ProgrammerAudience::class)->execute($aPlacer, $creneau);

        $this->fail('ValidationException attendue mais non levée.');
    } catch (ValidationException $e) {
        expect($e->errors()['creneau'])->toContain('Une audience ne peut pas être programmée dans le passé.');
    }

    expect($aPlacer->refresh())
        ->creneau->toBeNull()
        ->statut->toBe('en_attente');
})->with([
    // Lundi 31/08 à 10h : jour ouvré et heure valide, seule l'antériorité peut le faire refuser
    'la veille, jour et heure pourtant valides' => '2026-08-31T10:00',
    'un mois plus tôt' => '2026-08-05T14:00',
]);

test('accepte un créneau à venir dès le lendemain', function () {
    $aPlacer = Audience::create([
        'demandeur_nom' => 'Ibrahima Sow',
        'objet' => 'Coordination régionale',
        'recue_le' => now()->toDateString(),
        'statut' => 'en_attente',
        'saisie_par' => User::factory()->create()->id,
    ]);

    // Mercredi 02/09 à 10h, au lendemain de l'horloge figée
    $programmee = app(ProgrammerAudience::class)->execute($aPlacer, '2026-09-02T10:00');

    expect($programmee->creneau->toDateTimeString())->toBe('2026-09-02 10:00:00');
    expect($programmee->statut)->toBe('programmee');
});
