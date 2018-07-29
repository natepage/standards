<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Exceptions\BinaryNotFoundException;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Traits\ConfigAwareTrait;
use Symfony\Component\Process\Process;

abstract class WithConfigTool implements ConfigAwareInterface, ToolInterface
{
    use ConfigAwareTrait {
        setConfig as private traitSetConfig;
    }

    /**
     * Get tool description.
     *
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return null;
    }

    /**
     * Set config.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->traitSetConfig($config);

        // Set enabled option
        $config->addOption(new ConfigOption('enabled', true), $this->getId());

        $this->defineOptions($config);
    }

    /**
     * Define tool options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    abstract protected function defineOptions(ConfigInterface $config): void;

    /**
     * Resolve given binary or return null.
     *
     * @param null|string $binary
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary not found
     */
    protected function resolveBinary(?string $binary = null): string
    {
        $binary = $binary ?? $this->getId();

        $vendor = \sprintf('vendor/bin/%s', $binary);

        if (\file_exists($vendor)) {
            return $vendor;
        }

        $process = new Process(\sprintf('command -v %s', $binary));
        $process->run();
        $command = $process->getOutput();

        if (empty($command) === false && $process->isSuccessful()) {
            return $command;
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
