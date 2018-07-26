<?php
declare(strict_types=1);

/**
 * Require given filename if it exists, and return if it's been required.
 *
 * @param string $filename
 *
 * @return bool
 */
function requireIfExists(string $filename): bool
{
    if (\file_exists($filename) === false) {
        return false;
    }

    require_once $filename;

    return true;
}

$asProject = __DIR__ . '/../vendor/autoload.php';
$asDependency = __DIR__ . '/../../../autoload.php';

if ((\requireIfExists($asProject) || \requireIfExists($asDependency)) === false) {
    echo 'No autoload file found, please make sure natepage/standards is installed globally or in a composer project';

    exit(1);
}
