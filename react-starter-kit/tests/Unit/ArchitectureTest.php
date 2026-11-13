<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;

/*
 * Architecture tests as a starting point: Laravel's conventions (Pest's Laravel preset), no debugging
 * leftovers, strict types, no env() in app/, input in form requests and output in resources.
 * Adapt them to the project: add checks, or leave a class out with ->ignoring() when there is a reason.
 */

arch('the code follows Laravel\'s conventions for folders, names and classes')
    ->preset()->laravel();

arch('no debugging leftovers')
    ->expect(['dd', 'dump', 'ddd', 'ray', 'var_dump', 'print_r', 'die', 'exit'])
    ->not->toBeUsed();

arch('every class uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('settings are read from config, never with env() in the code')
    ->expect('env')
    ->not->toBeUsedIn('App');

arch('input is validated in form requests')
    ->expect('App\Http\Requests')
    ->toExtend(FormRequest::class);

arch('output goes through resources')
    ->expect('App\Http\Resources')
    ->toExtend(JsonResource::class);
