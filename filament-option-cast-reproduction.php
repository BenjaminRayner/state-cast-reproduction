<?php

require ($argv[1] ?? __DIR__ . '/vendor/autoload.php');

use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;

$singleCast = new OptionStateCast;
$multipleCast = new OptionsArrayStateCast;
$results = [];

foreach (['123', '00123', 'ABC'] as $input) {
    $single = $singleCast->get($input);
    $multiple = $multipleCast->get([$input]);

    $results[] = [
        'input' => $input,
        'single_value' => $single,
        'single_type' => get_debug_type($single),
        'multiple_value' => $multiple,
        'multiple_item_type' => get_debug_type($multiple[0]),
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
