<?php
declare(strict_types=1);

namespace NatePage\Standards\DependencyInjection;

use NatePage\Standards\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Class ContainerFactoryTest
 *
 * @covers \NatePage\Standards\DependencyInjection\ContainerFactory
 */
class ContainerFactoryTest extends TestCase
{
    /**
     * Should return ContainerInterface.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testCreate(): void
    {
        $factory = new ContainerFactory();

        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf(ContainerInterface::class, $factory->create([]));
    }
}
