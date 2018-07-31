<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Interfaces\ConfigInterface;

class PhpMd extends WithConfigTool
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
        $config = $this->config->dump();
        $rules = \file_exists('phpmd.xml') ? 'phpmd.xml' : $config['phpmd.rule_sets'];

        return \sprintf('%s %s text %s', $this->resolveBinary(), $config['paths'], $rules);
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
        return 'PHPMD';
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
        $config->addOption(
            new ConfigOption('rule_sets', 'cleancode,codesize,controversial,design,naming,unusedcode'),
            $this->getId()
        );
    }
}
