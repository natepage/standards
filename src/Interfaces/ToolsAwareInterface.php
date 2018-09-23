<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ToolsAwareInterface
{
    /**
     * Add tool.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     *
     * @return void
     */
    public function addTool(ToolInterface $tool): void;
}
