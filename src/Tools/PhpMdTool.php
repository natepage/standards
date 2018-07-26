<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PhpMdTool extends WithSymfonyProcessConfigTool
{
    /**
     * Get command line to execute the tool.
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary not found
     */
    public function getCli(): string
    {
        $config = $this->config->allFlat();
        $rules = \file_exists('phpmd.xml') ? 'phpmd.xml' : $config['phpmd.rule_sets'];

        return \sprintf('%s %s text %s', $this->resolveBinary(), $config['standards.paths'], $rules);
    }

    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return 'phpmd';
    }

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHP: Mess Detector';
    }

    /**
     * Define the config structure using the given node definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root
     *
     * @return void
     */
    protected function defineConfigStructure(ArrayNodeDefinition $root): void
    {
        $root
            ->canBeDisabled()
                ->children()
                    ->scalarNode('rule_sets')
                    ->beforeNormalization()
                        ->ifArray()
                        ->then(function (array $value): string { return \implode(',', $value); })
                    ->end()
                    ->defaultValue('cleancode,codesize,controversial,design,naming,unusedcode')
                ->end()
            ->end();
    }
}
