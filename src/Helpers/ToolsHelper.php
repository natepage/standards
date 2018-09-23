<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Interfaces\ConfigInterface;

class ToolsHelper
{
    /**
     * Get only enabled tools based on given config.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     * @param \NatePage\Standards\Interfaces\ToolInterface[] $tools
     *
     * @return \NatePage\Standards\Interfaces\ToolInterface[]
     */
    public function getEnabledTools(ConfigInterface $config, array $tools): array
    {
        $return = [];

        foreach ($tools as $tool) {
            if ($config->getValue(\strtolower(\sprintf('%s.enabled', $tool->getName()))) === false) {
                continue;
            }

            $return[] = $tool;
        }

        return $return;
    }
}
