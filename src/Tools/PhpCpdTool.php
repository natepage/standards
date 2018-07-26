<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PhpCpdTool extends WithSymfonyProcessConfigTool
{
    /**
     * Get command line to execute the tool.
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    public function getCli(): string
    {
        $config = $this->config->allFlat();

        return \sprintf(
            '%s --ansi --min-lines=%s --min-tokens=%s %s',
            $this->resolveBinary(),
            $config['phpcpd.min_lines'],
            $config['phpcpd.min_tokens'],
            $this->spacePaths($config['standards.paths'])
        );
    }

    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return 'phpcpd';
    }

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHPCPD';
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
                ->integerNode('min_lines')->defaultValue(5)->end()
                ->integerNode('min_tokens')->defaultValue(70)->end()
            ->end();
    }
}
