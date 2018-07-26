<?php
declare(strict_types=1);

namespace NatePage\Standards\Traits;

use NatePage\Standards\Interfaces\ConfigInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

trait UsesSymfonyConfig
{
    /**
     * @var \NatePage\Standards\Interfaces\ConfigInterface
     */
    protected $config;

    /**
     * Configure the current instance.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function processConfig(ConfigInterface $config): void
    {
        $this->config = $config;

        $rootNodeName = $this->defineRootNodeName();
        $treeBuilder = new TreeBuilder();

        $this->defineConfigStructure($treeBuilder->root($rootNodeName));

        $config->set(
            $rootNodeName,
            (new Processor())->process($treeBuilder->buildTree(), $config->get($rootNodeName, []))
        );
    }

    /**
     * Define the config structure using the given node definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root
     *
     * @return void
     */
    abstract protected function defineConfigStructure(ArrayNodeDefinition $root): void;

    /**
     * Define the root node name.
     *
     * @return string
     */
    abstract protected function defineRootNodeName(): string;
}
