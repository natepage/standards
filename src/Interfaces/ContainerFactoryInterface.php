<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

use Psr\Container\ContainerInterface;

interface ContainerFactoryInterface
{
    /**
     * Create container for given config files.
     *
     * @param null|string[] $configFiles
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function create(?array $configFiles = null): ContainerInterface;
}
