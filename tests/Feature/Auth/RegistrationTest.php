<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration with missing or invalid fields is rejected', function () {
    $this->post('/register', ['name' => '', 'email' => 'not-an-email', 'password' => 'password', 'password_confirmation' => 'different'])
        ->assertSessionHasErrors(['name', 'email', 'password']);

    $this->assertGuest();
});
