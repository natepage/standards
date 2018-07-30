<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface KernelInterface extends ToolsAwareInterface
{
    /**
     * Run the current standards instance and return exit code.
     *
     * @return int
     *
     * @throws \Exception If something went wrong
     */
    public function run(): int;
}
