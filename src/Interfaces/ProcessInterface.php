<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ProcessInterface
{
    /**
     * Get process output.
     *
     * @return string
     */
    public function getOutput(): string;

    /**
     * Check if process currently running.
     *
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * Check if process is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * Starts the process.
     *
     * The callback receives the type of output (out or err) and some bytes from
     * the output in real-time while writing the standard input to the process.
     * It allows to have feedback from the independent process during execution.
     *
     * @param null|callable $callback
     * @param null|mixed[] $env
     *
     * @return void
     */
    public function start(?callable $callback = null, ?array $env = null): void;
}
