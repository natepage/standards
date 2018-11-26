<?php
/** @noinspection PhpUnhandledExceptionInspection Handled in standards */
declare(strict_types=1);

use NatePage\Standards\DependencyInjection\ContainerFactory;
use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;

ConfigFileFinder::detectFromInput('standards', new ArgvInput());
$configFile = ConfigFileFinder::provide('standards', ['standards.yaml', 'standards.yml']);

return (new ContainerFactory())->create($configFile === null ? null : [$configFile]);
