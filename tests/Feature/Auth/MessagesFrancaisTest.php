<?php

use App\Models\User;

test('la locale de l\'application est le français', function () {
    expect(app()->getLocale())->toBe('fr');
});

test('des identifiants invalides renvoient le message français', function () {
    User::factory()->create(['email' => 'secretaire@dg.sn']);

    $this->post('/login', [
        'email' => 'secretaire@dg.sn',
        'password' => 'mauvais-mot-de-passe',
    ])->assertSessionHasErrors([
        'email' => 'Ces identifiants ne correspondent à aucun compte.',
    ]);

    $this->assertGuest();
});

test('le message de limitation est traduit en français', function () {
    expect(__('auth.throttle', ['seconds' => 42]))
        ->toBe('Trop de tentatives. Réessayez dans 42 secondes.');
});

test('au-delà de cinq tentatives le formulaire affiche le blocage en français', function () {
    User::factory()->create(['email' => 'secretaire@dg.sn']);

    foreach (range(1, 5) as $tentative) {
        $this->post('/login', [
            'email' => 'secretaire@dg.sn',
            'password' => 'mauvais-mot-de-passe',
        ])->assertSessionHasErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte.',
        ]);
    }

    $this->from('/login')
        ->followingRedirects()
        ->post('/login', [
            'email' => 'secretaire@dg.sn',
            'password' => 'mauvais-mot-de-passe',
        ])
        ->assertOk()
        ->assertSee('Trop de tentatives. Réessayez dans', false)
        ->assertSee('secondes.', false);

    $this->assertGuest();
});

test('le blocage persiste même avec le bon mot de passe', function () {
    User::factory()->create(['email' => 'secretaire@dg.sn']);

    foreach (range(1, 6) as $tentative) {
        $this->post('/login', [
            'email' => 'secretaire@dg.sn',
            'password' => 'mauvais-mot-de-passe',
        ]);
    }

    $this->from('/login')
        ->followingRedirects()
        ->post('/login', [
            'email' => 'secretaire@dg.sn',
            'password' => 'password',
        ])
        ->assertSee('Trop de tentatives.', false);

    $this->assertGuest();
});
