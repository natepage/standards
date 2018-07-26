<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PhpCsTool extends WithSymfonyProcessConfigTool
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
        $showSniffName = $config['phpcs.show_sniff_name'] ? '-s' : '';
        $standards = \file_exists('phpcs.xml') ? '' : \sprintf(
            '--standard=%s --report=full',
            $config['phpcs.standards']
        );

        return \sprintf(
            '%s %s --colors %s %s',
            $this->resolveBinary(),
            $standards,
            $this->spacePaths($config['standards.paths']),
            $showSniffName
        );
    }

    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return 'phpcs';
    }

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHPCS';
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
            ->scalarNode('standards')
            ->defaultValue('vendor/eoneopay/standards/php-code-sniffer/EoneoPay')
            ->end()
            ->booleanNode('show_sniff_name')->defaultValue(true)->end()
            ->end();
    }
}
