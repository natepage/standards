<?php
declare(strict_types=1);

namespace NatePage\Standards\Traits;

use NatePage\Standards\Interfaces\ToolInterface;

trait ToolsAwareTrait
{
    /**
     * @var \NatePage\Standards\Interfaces\ToolInterface[]
     */
    private $tools = [];

    /**
     * Add tool.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     *
     * @return void
     */
    public function addTool(ToolInterface $tool): void
    {
        $this->tools[] = $tool;
    }
}
