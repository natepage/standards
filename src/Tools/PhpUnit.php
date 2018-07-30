<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Interfaces\ConfigInterface;

class PhpUnit extends WithConfigTool
{
    /**
     * Get command line to execute the tool.
     *
     * @return string
     */
    public function getCli(): string
    {
//        $config = $this->config->dump();

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

    /**
     * Define tool options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    protected function defineOptions(ConfigInterface $config): void
    {
        $config->addOptions([
            new ConfigOption('enable_code_coverage', true),
            new ConfigOption('coverage_minimum_level', 90),
            new ConfigOption('junit_log_path', ''),
            new ConfigOption('test_directory', 'tests')
        ], $this->getId());
    }
}
