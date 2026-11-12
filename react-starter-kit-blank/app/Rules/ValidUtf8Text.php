<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Checks that a text is valid UTF-8 and contains no NUL byte.
 *
 * PostgreSQL refuses both: a query with broken bytes (e.g. half an "ü") fails with
 * "invalid byte sequence", and a NUL byte cannot be stored at all. Without this rule such
 * input, usually sent by attack tools, ends in an error page instead of a message.
 * Emoji and every other valid character are allowed.
 *
 * Use it for text that is saved or used in a database query, next to `string` and `max:`,
 * e.g. `['required', 'string', new ValidUtf8Text, 'max:255']`.
 *
 * Not for input that arrives in another encoding on purpose (e.g. an old system that sends
 * Latin-1): convert it to UTF-8 first (`mb_convert_encoding()`), then check it.
 */
class ValidUtf8Text implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Other types are the job of rules like `string`.
        if (! is_string($value)) {
            return;
        }

        if (! mb_check_encoding($value, 'UTF-8') || str_contains($value, "\0")) {
            $fail('validation.valid_utf8_text')->translate();
        }
    }
}
