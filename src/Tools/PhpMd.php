<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Process\Process;

class PhpMd extends AbstractTool
{
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
     * Get tool options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return [
            'config-file' => [
                'default' => 'phpmd.xml',
                'description' => 'Config file for PHP Mess Detector'
            ],
            'rule-sets' => [
                'default' => 'cleancode,codesize,controversial,design,naming,unusedcode',
                'description' => 'The rulesets to use to determine issues, will be ignored if config-file exists'
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
        $rules = \file_exists($configFile) ? $configFile : $this->getOptionValue('rule-sets');

        return new Process([
            $this->resolveBinary(),
            $this->config->getValue('paths'),
            'text',
            $rules
        ]);
    }
}
