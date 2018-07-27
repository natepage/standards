<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Interfaces\ToolsCollectionInterface;
use NatePage\Standards\ToolsCollection;

class DefaultToolsCollectionHelper
{
    /**
     * Return the default tools collection for standards.
     *
     * @return \NatePage\Standards\Interfaces\ToolsCollectionInterface
     */
    public function getTools(): ToolsCollectionInterface
    {
        $tools = new ToolsCollection();

        $tools->addTools([
            new \NatePage\Standards\Tools\PhpCsTool(),
            new \NatePage\Standards\Tools\PhpCpdTool(),
            new \NatePage\Standards\Tools\PhpStanTool()
        ]);
        $tools->addTools([
            new \NatePage\Standards\Tools\PhpUnitTool(),
            new \NatePage\Standards\Tools\PhpMdTool()
        ], 100);

        return $tools;
    }
}
