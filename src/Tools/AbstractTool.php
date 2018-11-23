<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Interfaces\BinaryAwareInterface;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Traits\BinaryAwareTrait;
use NatePage\Standards\Traits\ConfigAwareTrait;

abstract class AbstractTool implements BinaryAwareInterface, ConfigAwareInterface, ToolInterface
{
    use BinaryAwareTrait;
    use ConfigAwareTrait;

    /**
     * Build CLI array for process.
     *
     * @param mixed[] $cli
     *
     * @return mixed[]
     */
    protected function buildCli(array $cli): array
    {
        $commandLine = [];

        foreach ($cli as $item) {
            if (\is_array($item) === false) {
                $commandLine[] = $item;

                continue;
            }

            foreach ($item as $subItem) {
                $commandLine[] = $subItem;
            }
        }

        return $commandLine;
    }

    /**
     * Return array containing paths names.
     *
     * @param string $paths
     *
     * @return string[]
     */
    protected function explodePaths(string $paths): array
    {
        return \explode(',', $paths);
    }

    /**
     * Get option value for current tool.
     *
     * @param string $option
     *
     * @return mixed
     */
    protected function getOptionValue(string $option)
    {
        return $this->config->getValue(\strtolower(\sprintf('%s.%s', $this->getName(), $option)));
    }

    /**
     * Resolve binary.
     *
     * @param null|string $binary
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    protected function resolveBinary(?string $binary = null): string
    {
        return $this->binaryHelper->resolveBinary($binary ?? $this->getName());
    }

    /**
     * Return paths separated by spaces instead of commas.
     *
     * @param string $paths
     *
     * @return string
     */
    protected function spacePaths(string $paths): string
    {
        return \str_replace(',', ' ', $paths);
    }
}
