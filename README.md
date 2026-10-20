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

When a cast value becomes an integer query parameter, SQL Server can attempt to convert VARCHAR column values to integers. This can fail if the same column contains alphanumeric codes.

The cast conversion is the independently runnable reproduction in this repository. It does not create a Filament panel or reproduce the full application UI.
