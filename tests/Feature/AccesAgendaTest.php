<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('agenda'))->assertRedirect(route('login'));
});

test('authenticated users land on the agenda', function () {
    $this->actingAs(User::factory()->create(['role' => 'secretaire']));

    $this->get(route('agenda'))->assertOk();
});

test('the agenda offers a way to log out', function () {
    $this->actingAs(User::factory()->create(['role' => 'secretaire']));

    $this->get(route('agenda'))
        ->assertOk()
        ->assertSee('Se déconnecter')
        ->assertSee(route('logout'), false);
});

test('logging out ends the session and returns to the login page', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('logout'))->assertRedirect(route('login'));

    $this->assertGuest();
    $this->get(route('agenda'))->assertRedirect(route('login'));
});
