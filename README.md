# Filament option state cast reproduction

Minimal Composer project demonstrating numeric string identifiers being converted to integers by `OptionStateCast::get()` and `OptionsArrayStateCast::get()`.

## Run

Requires PHP 8.5 and Composer 2. No application configuration or database is needed for the cast-level reproduction.

```sh
git clone https://github.com/BenjaminRayner/state-cast-bug.git
cd state-cast-bug
composer install
composer reproduce
```

For input `"123"`, the output contains:

```json
{
    "input": "123",
    "single_value": 123,
    "single_type": "int",
    "multiple_value": [123],
    "multiple_item_type": "int"
}
```

Inputs `"00123"` and `"ABC"` remain strings. The script calls the upstream casts directly and contains no application overrides.

## Why this matters

A numeric-looking identifier can still be a string database key. The reported application uses SQL Server tables with VARCHAR business-code keys and string-keyed Eloquent models.

When a cast value becomes an integer query parameter, SQL Server can attempt to convert VARCHAR column values to integers. This can fail if the same column contains alphanumeric codes.

The cast conversion is the independently runnable reproduction in this repository. It does not create a Filament panel or reproduce the full application UI.

## Optional SQL Server check

In a Laravel application with a SQL Server connection, the following read-only query demonstrates the database consequence without creating or modifying any table:

```php
use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Illuminate\Support\Facades\DB;

$sql = <<<'SQL'
SELECT [code]
FROM (
    VALUES (CAST('123' AS varchar(30))), (CAST('ABC' AS varchar(30)))
) AS [codes]([code])
WHERE [code] = ?
SQL;

DB::connection('sqlsrv')->select($sql, [(new OptionStateCast)->get('123')]);
```

Compare with passing string `'123'` directly as the parameter.

The original application has a logged SQL Server conversion failure from a relationship-backed single select. This repository independently demonstrates the shared cast behavior; it does not claim that a complete multi-select relationship UI reproduction has been verified.

SQL Server's [data type precedence documentation](https://learn.microsoft.com/en-us/sql/t-sql/data-types/data-type-precedence-transact-sql) explains the conversion.
