<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Interfaces\ConfigInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class InputDefinitionHelper
{
    /**
     * Add given config options to given input definition.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     * @param \Symfony\Component\Console\Input\InputDefinition $inputDefinition
     *
     * @return void
     */
    public function addOptions(ConfigInterface $config, InputDefinition $inputDefinition): void
    {
        foreach ($config->getAllOptions() as $name => $option) {
            $inputDefinition->addOption(new InputOption(
                $name,
                $option['shortcut'] ?? null,
                $option['mode'] ?? InputOption::VALUE_OPTIONAL,
                $option['description'] ?? '',
                $option['default'] ?? null
            ));
        }
    }
}
