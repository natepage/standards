<?php
declare(strict_types=1);

namespace NatePage\Standards\DependencyInjection\CompilerPasses;

use NatePage\Standards\Console\Application;
use NatePage\Standards\Helpers\BinaryHelper;
use NatePage\Standards\Interfaces\BinaryAwareInterface;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Interfaces\ToolsAwareInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

class CollectorsCompilerPass implements CompilerPassInterface
{
    /**
     * @var mixed[]
     */
    private static $calls = [
        [Application::class, Command::class, 'add'],
        [BinaryAwareInterface::class, BinaryHelper::class, 'setBinaryHelper'],
        [ConfigAwareInterface::class, ConfigInterface::class, 'setConfig'],
        [ToolsAwareInterface::class, ToolInterface::class, 'addTool']
    ];

    /**
     * @var \Symplify\PackageBuilder\DependencyInjection\DefinitionCollector
     */
    private $definitionCollector;

    /**
     * CollectorsCompilerPass constructor.
     */
    public function __construct()
    {
        $this->definitionCollector = new DefinitionCollector(new DefinitionFinder());
    }

    /**
     * Add commands to application.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        foreach (static::$calls as $call) {
            $this->definitionCollector->loadCollectorWithType($container, $call[0], $call[1], $call[2]);
        }
    }
}
