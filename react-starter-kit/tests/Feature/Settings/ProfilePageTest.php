<?php

declare(strict_types=1);

use App\Concerns\ProfileValidationRules;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\User;

mutates(ProfileController::class, ProfileUpdateRequest::class, ProfileValidationRules::class);

test('saving the profile stores it and shows the message "Profile updated."', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), ['name' => 'Ada Lovelace', 'email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Profile updated.']);

    expect($user->fresh()->name)->toBe('Ada Lovelace');
});

test('the own email address can be kept, another user\'s address cannot be taken', function () {
    $user = User::factory()->create(['email' => 'ada@example.com']);
    User::factory()->create(['email' => 'grace@example.com']);

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), ['name' => 'Ada', 'email' => 'ada@example.com'])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), ['name' => 'Ada', 'email' => 'grace@example.com'])
        ->assertSessionHasErrors(['email' => 'The email has already been taken.']);

    expect($user->fresh()->email)->toBe('ada@example.com');
});

test('the profile is not changed with invalid input', function (array $input, string $field, string $message) {
    $user = User::factory()->create(['name' => 'Ada', 'email' => 'ada@example.com']);

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), $input)
        ->assertSessionHasErrors([$field => $message]);

    expect($user->fresh()->only('name', 'email'))->toBe(['name' => 'Ada', 'email' => 'ada@example.com']);
})->with([
    'name missing' => [['email' => 'new@example.com'], 'name', 'The name field is required.'],
    'name not text' => [['name' => ['Ada'], 'email' => 'new@example.com'], 'name', 'The name field must be a string.'],
    'name too long' => [['name' => str_repeat('a', 256), 'email' => 'new@example.com'], 'name', 'The name field must not be greater than 255 characters.'],
    'name with broken bytes' => [['name' => "Ad\xC3\x28", 'email' => 'new@example.com'], 'name', 'The name field must be valid UTF-8 text.'],
    'email with broken bytes' => [['name' => 'Grace', 'email' => "gr\xC3\x28@example.com"], 'email', 'The email field must be valid UTF-8 text.'],
    'email missing' => [['name' => 'Grace'], 'email', 'The email field is required.'],
    'email not text' => [['name' => 'Grace', 'email' => ['new@example.com']], 'email', 'The email field must be a string.'],
    'email invalid' => [['name' => 'Grace', 'email' => 'not-an-email'], 'email', 'The email field must be a valid email address.'],
    'email too long' => [['name' => 'Grace', 'email' => str_repeat('a', 244).'@example.com'], 'email', 'The email field must not be greater than 255 characters.'],
]);

test('deleting the account ends the session: its data is gone', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['remembered' => 'value of the old session'])
        ->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertSessionMissing('remembered')
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});
