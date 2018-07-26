<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ToolsCollectionInterface
{
    /**
     * Add tool to collection.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     * @param int|null $priority
     *
     * @return \NatePage\Standards\Interfaces\ToolsCollectionInterface
     */
    public function addTool(ToolInterface $tool, ?int $priority = null): self;

    /**
     * Add multiple tools to collection with the same priority.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface[] $tools
     * @param int|null $priority
     *
     * @return \NatePage\Standards\Interfaces\ToolsCollectionInterface
     */
    public function addTools(array $tools, ?int $priority = null): self;

    /**
     * Get all tools sorted by priority.
     *
     * @return \NatePage\Standards\Interfaces\ToolInterface[]
     */
    public function all(): array;

    /**
     * Check if collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
