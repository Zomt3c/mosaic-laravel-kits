<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Inertia\Inertia;

/**
 * Clears the encrypted Inertia browser history whenever someone logs in (password, two-factor,
 * passkey, registration or "remember me"), so a user who takes over a tab whose previous
 * session ended without the logout button cannot go back to the previous user's pages.
 */
class ClearInertiaHistory
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        Inertia::clearHistory();
    }
}
