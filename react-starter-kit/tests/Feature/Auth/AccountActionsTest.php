<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

mutates(CreateNewUser::class, ResetUserPassword::class, ProfileValidationRules::class, PasswordValidationRules::class);

test('registering stores the name and the email address that were entered', function () {
    $this->post(route('register.store'), [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors();

    $user = User::query()->sole();
    expect($user->name)->toBe('Ada Lovelace')
        ->and($user->email)->toBe('ada@example.com')
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

test('nobody is registered with invalid input', function (array $input, string $field, string $message) {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post(route('register.store'), $input)->assertSessionHasErrors([$field => $message]);

    expect(User::query()->count())->toBe(1);
})->with([
    'name missing' => [['email' => 'ada@example.com', 'password' => 'password', 'password_confirmation' => 'password'], 'name', 'The name field is required.'],
    'email taken' => [['name' => 'Ada', 'email' => 'taken@example.com', 'password' => 'password', 'password_confirmation' => 'password'], 'email', 'The email has already been taken.'],
    'password not confirmed' => [['name' => 'Ada', 'email' => 'ada@example.com', 'password' => 'password', 'password_confirmation' => 'other-password'], 'password', 'The password field confirmation does not match.'],
]);

test('resetting the password stores the new password', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('the password is not reset to a password that breaks the rules', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'short12',
        'password_confirmation' => 'short12',
    ])->assertSessionHasErrors(['password' => 'The password field must be at least 8 characters.']);

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});
