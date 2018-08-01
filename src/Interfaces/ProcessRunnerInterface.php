<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ProcessRunnerInterface extends ConsoleAwareInterface, RunnerInterface
{
    /**
     * Set process.
     *
     * @param \NatePage\Standards\Interfaces\ProcessInterface
     *
     * @return \NatePage\Standards\Interfaces\ProcessRunnerInterface
     */
    public function setProcess(ProcessInterface $process): self;
}
