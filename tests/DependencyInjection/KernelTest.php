<?php
declare(strict_types=1);

namespace NatePage\Standards\DependencyInjection;

use Mockery\MockInterface;
use NatePage\Standards\DependencyInjection\CompilerPasses\CollectorsCompilerPass;
use NatePage\Standards\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;

/**
 * Class KernelTest
 *
 * @covers \NatePage\Standards\DependencyInjection\Kernel
 */
class KernelTest extends TestCase
{
    /**
     * Description here
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testBuild(): void
    {
        /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
        $container = $this->mock(
            ContainerBuilder::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('addCompilerPass')
                    ->once()
                    ->withArgs(function ($arg): bool {
                        self::assertInstanceOf(CollectorsCompilerPass::class, $arg);

                        return true;
                    })->andReturnSelf();

                $mock->shouldReceive('addCompilerPass')
                    ->once()
                    ->withArgs(function ($arg): bool {

                        self::assertInstanceOf(AutowireSinglyImplementedCompilerPass::class, $arg);

                        return true;
                    })->andReturnSelf();
            }
        );

        $method = $this->getMethodAsPublic(Kernel::class, 'build');

        $method->invoke(new Kernel(), $container);
    }

    /**
     * Should register container configuration.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testRegisterContainerConfiguration(): void
    {
        /** @var \Symfony\Component\Config\Loader\LoaderInterface $loader */
        $loader = $this->mock(
            LoaderInterface::class,
            function (MockInterface $mock): void {
                $mock->shouldReceive('load')
                    ->once()
                    ->withArgs(function ($config): bool {
                        self::assertStringEndsWith('/../../config/config.yaml', $config);

                        return true;
                    })->andReturnNull();

                $mock->shouldReceive('load')
                    ->once()
                    ->with('extra_config.yml')
                    ->andReturnNull();
            }
        );

        $kernel = new Kernel(['extra_config.yml']);

        $kernel->registerContainerConfiguration($loader);
    }
}
