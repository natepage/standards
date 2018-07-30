<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Interfaces\ConfigInterface;

class PhpStan extends WithConfigTool
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
        $config = $this->config->dump();
        $neonFile = \file_exists('phpstan.neon') ? '-c phpstan.neon' : '';

        return \sprintf(
            '%s analyze %s %s --ansi --level %d --no-progress',
            $this->resolveBinary(),
            $this->spacePaths($config['paths']),
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
     * Define tool options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    protected function defineOptions(ConfigInterface $config): void
    {
        $config->addOption(new ConfigOption('reporting_level', 7), $this->getId());
    }
}
