<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use Symfony\Component\Process\Process;

class SecurityChecker extends AbstractTool
{
    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'SECURITY-CHECKER';
    }

    /**
     * Get tool options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return [];
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
        return new Process([$this->resolveBinary(), 'security:check']);
    }
}