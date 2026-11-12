<?php

declare(strict_types=1);

use App\Concerns\PasswordValidationRules;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

mutates(SecurityController::class, PasswordUpdateRequest::class, PasswordValidationRules::class);

test('a new password is stored and shows the message "Password updated."', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('security.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('security.edit'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Password updated.']);

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('the password is not changed with invalid input', function (array $input, string $field, string $message) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('security.edit'))
        ->put(route('user-password.update'), $input)
        ->assertSessionHasErrors([$field => $message]);

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
})->with([
    'current password missing' => [['password' => 'new-password', 'password_confirmation' => 'new-password'], 'current_password', 'The current password field is required.'],
    'current password wrong' => [['current_password' => 'wrong-password', 'password' => 'new-password', 'password_confirmation' => 'new-password'], 'current_password', 'The password is incorrect.'],
    'new password missing' => [['current_password' => 'password'], 'password', 'The password field is required.'],
    'new password not text' => [['current_password' => 'password', 'password' => ['new-password'], 'password_confirmation' => ['new-password']], 'password', 'The password field must be a string.'],
    'new password too short' => [['current_password' => 'password', 'password' => 'short12', 'password_confirmation' => 'short12'], 'password', 'The password field must be at least 8 characters.'],
    'new password not confirmed' => [['current_password' => 'password', 'password' => 'new-password', 'password_confirmation' => 'other-password'], 'password', 'The password field confirmation does not match.'],
]);
