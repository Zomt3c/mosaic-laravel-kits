<?php

declare(strict_types=1);

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Responses\LogoutResponse;
use App\Listeners\ClearInertiaHistory;
use App\Models\User;
use App\Providers\FortifyServiceProvider;

mutates(LogoutResponse::class, ProfileController::class, ClearInertiaHistory::class, FortifyServiceProvider::class);

test('every page tells the browser to store its history encrypted', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    expect($response->inertiaPage()['encryptHistory'] ?? false)->toBeTrue();
});

test('an ordinary page visit does not clear the browser history', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    expect($response->inertiaPage()['clearHistory'] ?? false)->toBeFalse();
});

test('after logging out the next page clears the browser history, the page after it does not', function () {
    $user = User::factory()->create();

    $afterLogout = $this->actingAs($user)->followingRedirects()->post(route('logout'));
    $nextVisit = $this->get(route('home'));

    $afterLogout->assertOk();
    $this->assertGuest();
    expect($afterLogout->inertiaPage()['component'])->toBe('welcome')
        ->and($afterLogout->inertiaPage()['clearHistory'] ?? false)->toBeTrue()
        ->and($nextVisit->inertiaPage()['clearHistory'] ?? false)->toBeFalse();
});

test('after deleting the account the next page clears the browser history', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->followingRedirects()->delete(route('profile.destroy'), ['password' => 'password']);

    $response->assertOk();
    expect($user->fresh())->toBeNull()
        ->and($response->inertiaPage()['component'])->toBe('welcome')
        ->and($response->inertiaPage()['clearHistory'] ?? false)->toBeTrue();
});

test('a wrong password when deleting the account keeps the browser history', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->followingRedirects()
        ->delete(route('profile.destroy'), ['password' => 'wrong-password']);

    expect($user->fresh())->not->toBeNull()
        ->and($response->inertiaPage()['clearHistory'] ?? false)->toBeFalse();
});

test('after logging in the next page clears the browser history', function () {
    $user = User::factory()->create();

    $response = $this->followingRedirects()->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk();
    $this->assertAuthenticatedAs($user);
    expect($response->inertiaPage()['component'])->toBe('dashboard')
        ->and($response->inertiaPage()['clearHistory'] ?? false)->toBeTrue();
});
