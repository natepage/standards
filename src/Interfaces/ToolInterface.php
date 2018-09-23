<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

use Symfony\Component\Process\Process;

interface ToolInterface
{
    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get tool options.
     *
     * @return mixed[]
     */
    public function getOptions(): array;

    /**
     * Get process to run.
     *
     * @return \Symfony\Component\Process\Process
     */
    public function getProcess(): Process;
}
