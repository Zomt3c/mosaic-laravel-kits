<?php

declare(strict_types=1);

/**
 * Every key of a language file, nested keys joined with dots (e.g. "attributes.email").
 *
 * @param  array<string, mixed>  $lines
 * @return list<string>
 */
function translationKeys(array $lines, string $prefix = ''): array
{
    $keys = [];
    foreach ($lines as $key => $value) {
        $keys = is_array($value)
            ? [...$keys, ...translationKeys($value, $prefix.$key.'.')]
            : [...$keys, $prefix.$key];
    }

    return $keys;
}

/**
 * The languages of the project besides English, from the folders and JSON files in lang/.
 *
 * @return list<string>
 */
function otherLanguages(): array
{
    $folders = array_map('basename', glob(lang_path('*'), GLOB_ONLYDIR) ?: []);
    $files = array_map(fn (string $file): string => basename($file, '.json'), glob(lang_path('*.json')) ?: []);

    return array_values(array_diff(array_unique([...$folders, ...$files]), ['en', 'vendor']));
}

test('every language has every text the English one has', function () {
    $missing = [];

    foreach (otherLanguages() as $language) {
        foreach (glob(lang_path('en/*.php')) ?: [] as $file) {
            $name = basename($file);
            $other = lang_path("{$language}/{$name}");
            $present = is_file($other) ? translationKeys(require $other) : [];

            foreach (array_diff(translationKeys(require $file), $present) as $key) {
                $missing[] = "{$language}/{$name}: {$key}";
            }
        }

        if (is_file(lang_path('en.json'))) {
            $other = lang_path("{$language}.json");
            $present = is_file($other) ? array_keys(json_decode((string) file_get_contents($other), true) ?: []) : [];

            foreach (array_diff(array_keys(json_decode((string) file_get_contents(lang_path('en.json')), true) ?: []), $present) as $key) {
                $missing[] = "{$language}.json: {$key}";
            }
        }
    }

    expect($missing)->toBe([]);
});
