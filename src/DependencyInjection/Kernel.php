<?php
declare(strict_types=1);

namespace NatePage\Standards\DependencyInjection;

use NatePage\Standards\DependencyInjection\CompilerPasses\CollectorsCompilerPass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

class Kernel extends BaseKernel
{
    use SimpleKernelTrait;

    /**
     * @var string[]
     */
    private $extraConfigFiles;

    /**
     * Kernel constructor.
     *
     * @param null|string[] $extraConfigFiles
     */
    public function __construct(?array $extraConfigFiles = null)
    {
        $this->extraConfigFiles = $extraConfigFiles ?? [];

        parent::__construct(\sprintf('stds_%s', \md5(\serialize($this->extraConfigFiles))), true);
    }

    /**
     * Loads the container configuration.
     *
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     *
     * @return void
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yaml');

        foreach ($this->extraConfigFiles as $configFile) {
            $loader->load($configFile);
        }
    }

    /**
     * Add compiler passes.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \ReflectionException
     */
    protected function build(ContainerBuilder $container): void
    {
        $container
            ->addCompilerPass(new CollectorsCompilerPass())
            ->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
