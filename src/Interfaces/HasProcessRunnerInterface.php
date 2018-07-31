<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface HasProcessRunnerInterface
{
    /**
     * Get process runner.
     *
     * @return \NatePage\Standards\Interfaces\ProcessRunnerInterface
     */
    public function getProcessRunner(): ProcessRunnerInterface;
}
