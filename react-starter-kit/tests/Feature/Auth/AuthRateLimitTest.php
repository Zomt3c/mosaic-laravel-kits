<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\FortifyServiceProvider;

mutates(FortifyServiceProvider::class);

test('five wrong passwords within a minute are answered, the sixth attempt is refused', function () {
    $user = User::factory()->create();
    $attempt = fn () => $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password']);

    foreach (range(1, 5) as $try) {
        $attempt()->assertSessionHasErrors('email');
    }

    $attempt()->assertTooManyRequests();
});

test('five wrong two-factor codes within a minute are answered, the sixth is refused, another person may still try', function () {
    $user = User::factory()->withTwoFactor()->create();
    $other = User::factory()->withTwoFactor()->create();
    $attempt = fn (User $as) => $this->withSession(['login.id' => $as->id])
        ->post(route('two-factor.login.store'), ['recovery_code' => 'wrong-code']);

    foreach (range(1, 5) as $try) {
        $attempt($user)->assertRedirect();
    }

    $attempt($user)->assertTooManyRequests();
    $attempt($other)->assertRedirect();
});

test('ten passkey requests within a minute are answered, the eleventh is refused, another passkey may still ask', function () {
    $options = fn (string $credential) => $this->get(route('passkey.login-options', ['credential' => ['id' => $credential]]));

    foreach (range(1, 10) as $try) {
        $options('credential-1')->assertOk();
    }

    $options('credential-1')->assertTooManyRequests();
    $options('credential-2')->assertOk();
});
