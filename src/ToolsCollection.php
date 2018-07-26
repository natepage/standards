<?php
declare(strict_types=1);

namespace NatePage\Standards;

use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Interfaces\ToolsCollectionInterface;

class ToolsCollection implements ToolsCollectionInterface
{
    /**
     * @var \NatePage\Standards\Interfaces\ToolInterface[]
     */
    private $tools = [];

    /**
     * Add tool to collection.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     * @param int|null $priority
     *
     * @return \NatePage\Standards\Interfaces\ToolsCollectionInterface
     */
    public function addTool(ToolInterface $tool, ?int $priority = null): ToolsCollectionInterface
    {
        $priority = $priority ?? 0;

        if (isset($this->tools[$priority]) === false) {
            $this->tools[$priority] = [];
        }

        $this->tools[$priority][] = $tool;

        return $this;
    }

    /**
     * Add multiple tools to collection with the same priority.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface[] $tools
     * @param int|null $priority
     *
     * @return \NatePage\Standards\Interfaces\ToolsCollectionInterface
     */
    public function addTools(array $tools, ?int $priority = null): ToolsCollectionInterface
    {
        foreach ($tools as $tool) {
            $this->addTool($tool, $priority);
        }

        return $this;
    }

    /**
     * Get all tools sorted by priority.
     *
     * @return \NatePage\Standards\Interfaces\ToolInterface[]
     */
    public function all(): array
    {
        \krsort($this->tools);

        return \array_merge(...$this->tools);
    }

    /**
     * Check if collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->tools);
    }
}
