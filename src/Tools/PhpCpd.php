<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Process\Process;

class PhpCpd extends AbstractTool
{
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
     * Get tool options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return [
            'min-lines' => [
                'default' => 5,
                'description' => 'The minimum number of lines which need to be duplicated to count as copy/paste'
            ],
            'min-tokens' => [
                'default' => 70,
                'description' => 'The minimum number of duplicated tokens within a line to count as copy/paste'
            ]
        ];
    }

    /**
     * Get process to run.
     *
     * @return \Symfony\Component\Process\Process
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    public function getProcess(): Process
    {
        return new Process($this->buildCli([
            $this->resolveBinary(),
            '--ansi',
            \sprintf('--min-lines=%s', $this->getOptionValue('min-lines')),
            \sprintf('--min-tokens=%s', $this->getOptionValue('min-tokens')),
            $this->explodePaths($this->config->getValue('paths'))
        ]));
    }
}
