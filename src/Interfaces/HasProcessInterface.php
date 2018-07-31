<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

use Symfony\Component\Process\Process;

interface HasProcessInterface
{
    /**
     * Get process.
     *
     * @return \Symfony\Component\Process\Process
     */
    public function getProcess(): Process;
}
