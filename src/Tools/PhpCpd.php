<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Interfaces\ConfigInterface;

class PhpCpd extends WithConfigTool
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

        return \sprintf(
            '%s --ansi --min-lines=%s --min-tokens=%s %s',
            $this->resolveBinary(),
            $config['phpcpd.min_lines'],
            $config['phpcpd.min_tokens'],
            $this->spacePaths($config['paths'])
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
     * Define tool options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    protected function defineOptions(ConfigInterface $config): void
    {
        $config->addOptions([
            new ConfigOption(
                'min_lines',
                5,
                'The minimum number of lines which need to be duplicated to count as copy/paste'
            ),
            new ConfigOption(
                'min_tokens',
                70,
                'The minimum number of duplicated tokens within a line to count as copy/paste'
            )
        ], $this->getId());
    }
}
