<?php

use App\Actions\ConfirmerTenue;
use App\Actions\MarquerNonHonoree;
use App\Models\Audience;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-09-16 12:00:00');

    $this->secretaire = User::factory()->create(['role' => 'secretaire']);

    $this->audienceEchue = Audience::create([
        'demandeur_nom' => 'Amadou Diop',
        'objet' => 'Objet de démonstration',
        'recue_le' => '2026-09-01',
        'statut' => 'echue',
        'creneau' => '2026-09-15 10:00:00',
        'saisie_par' => $this->secretaire->id,
    ]);
});

test('ConfirmerTenue passe le dossier en tenue et trace son auteur', function () {
    $this->actingAs($this->secretaire);

    $audience = app(ConfirmerTenue::class)->execute($this->audienceEchue);

    expect($audience->statut)->toBe('tenue');

    $evenement = $audience->evenements()->where('type', 'tenue')->sole();

    expect($evenement->libelle)->toBe('Audience tenue');
    expect($evenement->auteur_id)->toBe($this->secretaire->id);
});

test('MarquerNonHonoree passe le dossier en non honorée avec le motif saisi', function () {
    $this->actingAs($this->secretaire);

    $audience = app(MarquerNonHonoree::class)->execute($this->audienceEchue, 'Demandeur souffrant.');

    expect($audience->statut)->toBe('non_honoree');

    $evenement = $audience->evenements()->where('type', 'non_honoree')->sole();

    expect($evenement->libelle)->toBe('Demandeur non présenté');
    expect($evenement->detail)->toBe('Demandeur souffrant.');
    expect($evenement->auteur_id)->toBe($this->secretaire->id);
});

test('MarquerNonHonoree accepte de ne consigner aucun motif', function () {
    $this->actingAs($this->secretaire);

    $audience = app(MarquerNonHonoree::class)->execute($this->audienceEchue);

    expect($audience->statut)->toBe('non_honoree');
    expect($audience->evenements()->where('type', 'non_honoree')->sole()->detail)->toBeNull();
});

test('le créneau est conservé pour rester consultable dans l\'agenda', function (string $action) {
    $this->actingAs($this->secretaire);

    $audience = $action === 'tenue'
        ? app(ConfirmerTenue::class)->execute($this->audienceEchue)
        : app(MarquerNonHonoree::class)->execute($this->audienceEchue);

    expect($audience->creneau->format('Y-m-d H:i'))->toBe('2026-09-15 10:00');
})->with(['tenue', 'non_honoree']);
