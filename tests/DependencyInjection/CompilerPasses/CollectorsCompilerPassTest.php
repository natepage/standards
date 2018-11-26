<?php
declare(strict_types=1);

namespace NatePage\Standards\DependencyInjection\CompilerPasses;

use Mockery\MockInterface;
use NatePage\Standards\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CollectorsCompilerPassTest
 *
 * @covers \NatePage\Standards\DependencyInjection\CompilerPasses\CollectorsCompilerPass
 */
class CollectorsCompilerPassTest extends TestCase
{
    /**
     * Description here
     *
     * @return void
     */
    public function testProcess(): void
    {
        /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder */
        $containerBuilder = $this->mock(
            ContainerBuilder::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('getDefinitions')
                    ->once()->withNoArgs()
                    ->andReturn([]);
            }
        );

        $compiler = new CollectorsCompilerPass();

        $compiler->process($containerBuilder);

        // What can i assert???
        self::assertTrue(true);
    }
}
