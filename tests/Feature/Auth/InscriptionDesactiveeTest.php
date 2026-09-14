<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('la fonctionnalité d\'inscription de Fortify est désactivée', function () {
    expect(Features::enabled(Features::registration()))->toBeFalse();
});

test('les routes d\'inscription ne sont plus enregistrées', function () {
    expect(Route::has('register'))->toBeFalse();
    expect(Route::has('register.store'))->toBeFalse();
});

test('la page d\'inscription n\'est plus accessible', function () {
    $this->get('/register')->assertNotFound();
});

test('on ne peut plus créer de compte en postant sur /register', function () {
    $this->post('/register', [
        'name' => 'Intrus',
        'email' => 'intrus@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    expect(User::where('email', 'intrus@example.com')->exists())->toBeFalse();
    $this->assertGuest();
});

test('la page de connexion ne propose aucun lien d\'inscription', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Se connecter')
        ->assertDontSee('inscrire', false)
        ->assertDontSee('Sign up', false)
        ->assertDontSee('/register', false);
});
