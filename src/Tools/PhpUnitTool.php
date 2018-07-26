<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PhpUnitTool extends WithSymfonyProcessConfigTool
{
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
                ->booleanNode('enable_code_coverage')->defaultValue(true)->end()
                ->integerNode('coverage_minimum_level')->defaultValue(90)->end()
                ->scalarNode('junit_log_path')->defaultValue('')->end()
                ->scalarNode('test_directory')->defaultValue('tests')->end()
            ->end()
        ;
    }

    /**
     * Get command line to execute the tool.
     *
     * @return string
     */
    public function getCli(): string
    {
//        $config = $this->config->allFlat();

        return '$(command -v phpdbg) -qrr vendor/bin/phpunit --bootstrap vendor/autoload.php --colors=always tests --coverage-text';
    }

    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return 'phpunit';
    }

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHPUNIT';
    }
}
