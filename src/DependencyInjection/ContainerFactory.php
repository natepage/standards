<?php
declare(strict_types=1);

namespace NatePage\Standards\DependencyInjection;

use NatePage\Standards\Interfaces\ContainerFactoryInterface;
use Psr\Container\ContainerInterface;

class ContainerFactory implements ContainerFactoryInterface
{
    /**
     * Create container for given config files.
     *
     * @param null|string[] $configFiles
     *
     * @return \Psr\Container\ContainerInterface
     *
     * @throws \ReflectionException
     */
    public function create(?array $configFiles = null): ContainerInterface
    {
        $kernel = new Kernel($configFiles);
        $kernel->boot();

        return $kernel->getContainer();
    }
}
