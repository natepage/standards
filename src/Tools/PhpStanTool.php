<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PhpStanTool extends WithSymfonyProcessConfigTool
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
        $neonFile = \file_exists('phpstan.neon') ? '-c phpstan.neon' : '';

        return \sprintf(
            '%s analyze %s %s --ansi --level %d --no-progress',
            $this->resolveBinary(),
            $this->spacePaths($config['standards.paths']),
            $neonFile,
            $config['phpstan.reporting_level']
        );
    }

    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return 'phpstan';
    }

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHPSTAN';
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
            ->integerNode('reporting_level')->defaultValue(7)->end()
            ->end();
    }
}
