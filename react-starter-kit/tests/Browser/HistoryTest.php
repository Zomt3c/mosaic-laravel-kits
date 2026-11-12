<?php

declare(strict_types=1);

use App\Models\User;

/**
 * Logs the user in through the login page and opens the profile settings, so a page with the
 * user's name and email address is in the browser history.
 */
function openProfileAfterLogin(User $user): mixed
{
    return visit(route('login'))
        ->type('email', $user->email)
        ->type('password', 'password')
        ->press('@login-button')
        ->assertPathIs('/dashboard')
        ->assertSee($user->name)
        ->navigate(route('profile.edit'))
        ->assertSee('Delete account')
        ->assertPathIs('/settings/profile');
}

/**
 * JavaScript that goes the given number of steps back in the browser history and resolves
 * once the address has changed and the page had half a second to show the restored page.
 * There is nothing visible to wait for when the private data is (correctly) not shown, hence the pause.
 */
function goBackInHistory(int $steps): string
{
    return 'new Promise((resolve) => { '
        .'const start = location.href; history.go(-'.$steps.'); '
        .'const check = () => location.href !== start ? setTimeout(() => resolve(true), 500) : setTimeout(check, 50); '
        .'check(); })';
}

/**
 * JavaScript that is true when the given text is anywhere in the browser's stored history state.
 */
function historyStateContains(string $text): string
{
    return 'JSON.stringify(window.history.state ?? null).includes("'.$text.'")';
}

test('after logging out, going back in the browser shows none of the private data', function () {
    $canary = 'canary-logout-'.fake()->bothify('????####');
    $user = User::factory()->create(['name' => $canary]);

    $page = openProfileAfterLogin($user)
        ->click('@sidebar-menu-button')
        ->click('@logout-button')
        ->assertSee('Log in')
        ->assertPathIs('/');

    $page->assertScript(goBackInHistory(1), true)
        ->assertDontSee($canary)
        ->assertScript(historyStateContains($canary), false);
});

test('after deleting the account, going back in the browser shows none of the private data', function () {
    $canary = 'canary-deleted-'.fake()->bothify('????####');
    $user = User::factory()->create(['name' => $canary]);

    $page = openProfileAfterLogin($user)
        ->click('@delete-user-button')
        ->type('password', 'password')
        ->click('@confirm-delete-user-button')
        ->assertSee('Log in')
        ->assertPathIs('/');

    expect($user->fresh())->toBeNull();

    $page->assertScript(goBackInHistory(1), true)
        ->assertDontSee($canary)
        ->assertScript(historyStateContains($canary), false);
});
