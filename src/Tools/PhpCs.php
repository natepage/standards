<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

class PhpCs extends WithConfigTool
{
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
     * {@inheritdoc}
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    protected function getCli(): string
    {
        $config = $this->config->dump();
        $showSniffName = $config['phpcs.show_sniff_name'] ? '-s' : '';
        $standards = \file_exists('phpcs.xml') ? '' : \sprintf(
            '--standard=%s --report=full',
            $config['phpcs.standards']
        );

        return \sprintf(
            '%s %s --colors %s %s',
            $this->resolveBinary(),
            $standards,
            $this->spacePaths($config['paths']),
            $showSniffName
        );
    }
}
