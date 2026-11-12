<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

mutates(AppServiceProvider::class);

test('dates are immutable', function () {
    expect(now())->toBeInstanceOf(CarbonImmutable::class);
});

test('in production a password needs twelve characters, upper and lower case, a number and a symbol', function (string $password, string $message) {
    app()->detectEnvironment(fn (): string => 'production');

    $errors = Validator::make(['password' => $password], ['password' => Password::default()])->errors()->all();

    expect($errors)->toContain($message);
})->with([
    'too short' => ['Ab1!', 'The password field must be at least 12 characters.'],
    'no upper case' => ['abcdefghij1!', 'The password field must contain at least one uppercase and one lowercase letter.'],
    'no letter' => ['123456789012!', 'The password field must contain at least one letter.'],
    'no number' => ['Abcdefghijk!', 'The password field must contain at least one number.'],
    'no symbol' => ['Abcdefghijk1', 'The password field must contain at least one symbol.'],
]);

test('in production a password from a known data leak is refused', function () {
    app()->detectEnvironment(fn (): string => 'production');
    $hash = strtoupper(sha1('Correct-Horse-1!'));
    Http::fake(['api.pwnedpasswords.com/*' => Http::response(substr($hash, 5).':42')]);

    $errors = Validator::make(['password' => 'Correct-Horse-1!'], ['password' => Password::default()])->errors()->all();

    expect($errors)->toContain('The given password has appeared in a data leak. Please choose a different password.');
});

test('in production destructive database commands are refused', function () {
    app()->detectEnvironment(fn (): string => 'production');
    (new AppServiceProvider(app()))->boot();

    $this->artisan('migrate:fresh', ['--force' => true])->assertFailed();
});
