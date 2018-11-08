<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Process\Process;

class Ecs extends AbstractTool
{
    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'ECS';
    }

    /**
     * Get tool options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return [
            'config-file' => [
                'default' => __DIR__ . '/../../config/ecs.yaml',
                'description' => 'Config file to use to run EasyCodingStandards'
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
        $configFile = $this->getOptionValue('config-file') ?? '';
        $ecsFile = \file_exists($configFile) ? \sprintf('--config=%s', $configFile) : '';

        return new Process($this->buildCli([
            $this->resolveBinary(),
            'check',
            $this->explodePaths($this->config->getValue('paths')),
            $ecsFile,
            '--clear-cache'
        ]));
    }
}
