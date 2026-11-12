<?php

declare(strict_types=1);

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Resources\UserResource;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

mutates(UserResource::class, HandleInertiaRequests::class);

test('pages receive only the public profile fields of the logged-in user', function () {
    $this->travelTo('2026-01-15 10:30:00');
    $user = User::factory()->create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]);

    $response = $this->actingAs($user->fresh())->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('auth.user', fn (Assert $shared) => $shared
            ->where('id', $user->id)
            ->where('name', 'Ada Lovelace')
            ->where('email', 'ada@example.com')
            ->where('email_verified_at', '2026-01-15T10:30:00.000000Z')
            ->where('created_at', '2026-01-15T10:30:00.000000Z')
            ->where('updated_at', '2026-01-15T10:30:00.000000Z'),
        ),
    );
});

test('guests receive no user', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page->where('auth.user', null));
});

test('pages receive the name of the app', function () {
    config(['app.name' => 'Mosaic Test App']);

    $response = $this->get(route('home'));

    $response->assertInertia(fn (Assert $page) => $page->where('name', 'Mosaic Test App'));
});
