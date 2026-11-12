<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

mutates(DatabaseSeeder::class);

/**
 * The number of rows in every table of the database, by table name.
 *
 * @return array<string, int>
 */
function rowCountOfEveryTable(): array
{
    return collect(Schema::getTables())
        ->mapWithKeys(fn (array $table): array => [$table['name'] => DB::table($table['name'])->count()])
        ->sortKeys()
        ->all();
}

test('the seeders create the test user', function () {
    $this->seed();

    expect(User::query()->where('email', 'test@example.com')->sole()->name)->toBe('Test User');
});

test('running all seeders again creates nothing twice', function () {
    $this->seed();
    $afterFirstRun = rowCountOfEveryTable();

    $this->seed();

    expect(rowCountOfEveryTable())->toBe($afterFirstRun);
});
