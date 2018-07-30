<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ToolsRunnerInterface extends ConsoleAwareInterface, ToolsAwareInterface, RunnerInterface
{
    /**
     * Check if all tools were successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool;
}
