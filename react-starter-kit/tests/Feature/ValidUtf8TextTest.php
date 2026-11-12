<?php

declare(strict_types=1);

use App\Rules\ValidUtf8Text;
use Illuminate\Support\Facades\Validator;

mutates(ValidUtf8Text::class);

test('valid text passes, also with umlauts and emoji', function (string $text) {
    expect(Validator::make(['text' => $text], ['text' => new ValidUtf8Text])->passes())->toBeTrue();
})->with([
    'plain' => ['Ada Lovelace'],
    'umlauts and emoji' => ['Grüße 👋'],
    'line breaks' => ["first line\nsecond line"],
]);

test('broken bytes and NUL bytes are refused with a message', function (string $text) {
    $validator = Validator::make(['text' => $text], ['text' => new ValidUtf8Text]);

    expect($validator->errors()->first('text'))->toBe('The text field must be valid UTF-8 text.');
})->with([
    'half an umlaut' => ["a\xC3\x28"],
    'NUL byte' => ["a\0b"],
]);

test('values that are no text are left to other rules', function () {
    expect(Validator::make(['text' => ['a list']], ['text' => new ValidUtf8Text])->passes())->toBeTrue();
});
