<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

use Symfony\Component\Process\Process;

interface ProcessRunnerInterface extends ConsoleAwareInterface, RunnerInterface
{
    /**
     * Set process.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \NatePage\Standards\Interfaces\ProcessRunnerInterface
     */
    public function setProcess(Process $process): self;
}
