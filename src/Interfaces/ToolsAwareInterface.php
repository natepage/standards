<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ToolsAwareInterface
{
    /**
     * Add tool.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     * @param int|null $priority
     *
     * @return void
     */
    public function addTool(ToolInterface $tool, ?int $priority = null): void;

    /**
     * Set tools.
     *
     * @param \NatePage\Standards\Interfaces\ToolsCollectionInterface $tools
     *
     * @return void
     */
    public function setTools(ToolsCollectionInterface $tools): void;
}
