<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Exceptions\BinaryNotFoundException;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Traits\ConfigAwareTrait;
use Symfony\Component\Process\Process;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

abstract class AbstractTool implements ToolInterface, ConfigAwareInterface
{
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
     * Resolve given binary or return null.
     *
     * @param null|string $binary
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary not found
     * @throws \ReflectionException
     */
    protected function resolveBinary(?string $binary = null): string
    {
        $binary = $binary ?? \strtolower($this->getName());

        // Try inspected project vendor
        $vendor = \sprintf('vendor/bin/%s', $binary);

        if (\file_exists($vendor)) {
            return $vendor;
        }

        // Try command line tool
        $process = new Process(\sprintf('command -v %s', $binary));
        $process->run();
        $command = $process->getOutput();

        if (empty($command) === false && $process->isSuccessful()) {
            return \trim($command);
        }

        // Fallback to local one
        $vendor = \sprintf('%s/bin/%s', VendorDirProvider::provide(), $binary);

        if (\file_exists($vendor)) {
            return $vendor;
        }

        throw new BinaryNotFoundException(\sprintf('Binary for %s not found.', $binary));
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
