<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

mutates(User::class);

test('only name, email address and password can be filled from input', function () {
    $user = new User([
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'password' => 'secret-password',
        'email_verified_at' => now(),
        'remember_token' => 'token',
    ]);

    expect($user->name)->toBe('Ada')
        ->and($user->email)->toBe('ada@example.com')
        ->and($user->password)->not->toBeNull()
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->remember_token)->toBeNull();
});

test('a password is stored only as a hash, never as plain text', function () {
    $user = User::factory()->create(['password' => 'plain-password']);

    $stored = $user->fresh()->getAttributes()['password'];

    expect($stored)->not->toBe('plain-password')
        ->and(Hash::check('plain-password', $stored))->toBeTrue();
});

test('secrets never leave the model as an array or JSON', function () {
    $user = User::factory()->create(['remember_token' => 'token']);

    expect($user->toArray())->not->toHaveKeys(['password', 'remember_token'])
        ->toHaveKeys(['name', 'email']);
});
