<?php
declare(strict_types=1);

$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor',
    // dependency
    __DIR__ . '/../../repositories',
    // monorepo
    __DIR__ . '/../../../vendor'
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\is_file($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        break;
    }
}
