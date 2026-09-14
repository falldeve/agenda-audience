<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::resetPasswords());
});

test('les cinq clés de réinitialisation sont traduites en français', function () {
    expect(__('passwords.sent'))->toBe('Le lien de réinitialisation a été envoyé par email.');
    expect(__('passwords.reset'))->toBe('Votre mot de passe a été réinitialisé.');
    expect(__('passwords.throttled'))->toBe('Veuillez patienter avant de réessayer.');
    expect(__('passwords.token'))->toBe('Ce jeton de réinitialisation est invalide.');
    expect(__('passwords.user'))->toBe('Aucun compte ne correspond à cette adresse email.');
});

test('la demande de lien affiche la confirmation française sur le formulaire', function () {
    Notification::fake();

    User::factory()->create(['email' => 'secretaire@dg.sn']);

    $this->from(route('password.request'))
        ->followingRedirects()
        ->post('/forgot-password', ['email' => 'secretaire@dg.sn'])
        ->assertOk()
        ->assertSee('Le lien de réinitialisation a été envoyé par email.', false);
});

test('une adresse inconnue renvoie un message français', function () {
    Notification::fake();

    $this->from(route('password.request'))
        ->followingRedirects()
        ->post('/forgot-password', ['email' => 'inconnu@dg.sn'])
        ->assertOk()
        ->assertSee('Aucun compte ne correspond à cette adresse email.', false);
});
