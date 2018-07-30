<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Interfaces\ToolsCollectionInterface;
use NatePage\Standards\Tools\ToolsCollection;

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
            new \NatePage\Standards\Tools\PhpCs(),
            new \NatePage\Standards\Tools\PhpCpd(),
            new \NatePage\Standards\Tools\PhpStan()
        ]);
        $tools->addTools([
            new \NatePage\Standards\Tools\PhpUnit(),
            new \NatePage\Standards\Tools\PhpMd()
        ], 100);

        return $tools;
    }
}
