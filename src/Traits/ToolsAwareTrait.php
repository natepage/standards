<?php
declare(strict_types=1);

namespace NatePage\Standards\Traits;

use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Interfaces\ToolsCollectionInterface;
use NatePage\Standards\ToolsCollection;

trait ToolsAwareTrait
{
    /**
     * @var \NatePage\Standards\Interfaces\ToolsCollectionInterface
     */
    protected $tools;

    /**
     * Add tool.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     * @param int|null $priority
     *
     * @return void
     */
    public function addTool(ToolInterface $tool, ?int $priority = null): void
    {
        if ($this->tools instanceof ToolsCollectionInterface) {
            $this->tools->addTool($tool, $priority);

            return;
        }

        $this->tools = (new ToolsCollection())->addTool($tool, $priority);
    }

    /**
     * Set tools.
     *
     * @param \NatePage\Standards\Interfaces\ToolsCollectionInterface $tools
     *
     * @return void
     */
    public function setTools(ToolsCollectionInterface $tools): void
    {
        $this->tools = $tools;
    }
}
