<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

class PhpMd extends WithConfigTool
{
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
     * {@inheritdoc}
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary not found
     */
    protected function getCli(): string
    {
        $config = $this->config->dump();
        $rules = \file_exists('phpmd.xml') ? 'phpmd.xml' : $config['phpmd.rule_sets'];

        return \sprintf('%s %s text %s', $this->resolveBinary(), $config['paths'], $rules);
    }
}
