<?php
declare(strict_types=1);

namespace NatePage\Standards\Processes;

use NatePage\Standards\Interfaces\ProcessInterface;
use Symfony\Component\Process\Process;

class CliProcess extends Process implements ProcessInterface
{
    /**
     * Get process output.
     *
     * @return string
     */
    public function getOutput(): string
    {
        return parent::getOutput();
    }

    /**
     * Check if process currently running.
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return parent::isRunning();
    }

    /**
     * Check if process is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return parent::isSuccessful();
    }

    /**
     * {@inheritdoc}
     */
    public function start(?callable $callback = null, ?array $env = null): void
    {
        parent::start($callback, $env ?? []);
    }
}
