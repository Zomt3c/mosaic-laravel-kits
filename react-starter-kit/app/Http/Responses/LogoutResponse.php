<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Http\Responses\LogoutResponse as FortifyLogoutResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's logout response that also tells the browser to forget the encrypted Inertia
 * history, so pressing Back after logging out never shows the previous user's pages.
 */
class LogoutResponse extends FortifyLogoutResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        Inertia::clearHistory();

        return parent::toResponse($request);
    }
}
