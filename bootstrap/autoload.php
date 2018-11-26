<?php
declare(strict_types=1);

$possibleAutoloadPaths = [
    // project dependency
    __DIR__ . '/../vendor',
    // global dependency
    __DIR__ . '/../../..'
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\is_file($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        break;
    }
}
