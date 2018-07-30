<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Exceptions\InvalidConfigOptionException;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Traits\ConfigAwareTrait;

class StandardsConfigHelper implements ConfigAwareInterface
{
    use ConfigAwareTrait;

    /**
     * Enable or disable tools based on config.
     *
     * @return \NatePage\Standards\Helpers\StandardsConfigHelper
     */
    public function handleToolsState(): self
    {
        $only = $this->config->get('only');

        if ($only === null || empty($only)) {
            return $this;
        }

        $only = \explode(',', $only);

        foreach ($this->getToolsId() as $toolId) {
            $this->config->set(\sprintf('%s.enabled', $toolId), \in_array($toolId, $only, true));
        }

        return $this;
    }

    /**
     * Check if given tool is enabled.
     *
     * @param string $toolId
     *
     * @return bool
     */
    public function isToolEnabled(string $toolId): bool
    {
        return (bool)$this->config->get(\sprintf('%s.enabled', $toolId));
    }

    /**
     * Merge config with given one.
     *
     * @param mixed[] $config
     *
     * @return \NatePage\Standards\Helpers\StandardsConfigHelper
     */
    public function merge(array $config): self
    {
        $this->config->merge($config);

        return $this;
    }

    /**
     * Set valid paths config with existing paths.
     *
     * @return self
     *
     * @throws \NatePage\Standards\Exceptions\InvalidConfigOptionException If not at least one paths exist
     */
    public function normalizePaths(): self
    {
        $found = [];
        $paths = $this->config->get('paths');

        foreach (\explode(',', $paths) as $path) {
            if (\is_dir($path) === false) {
                continue;
            }

            $found[] = $path;
        }

        if (empty($found) === false) {
            $this->config->set('paths', \implode(',', $found));

            return $this;
        }

        throw new InvalidConfigOptionException(\sprintf(
            'None of configured paths has been found. Paths: %s',
            $paths
        ));
    }

    /**
     * Get tool ids.
     *
     * @return string[]
     */
    private function getToolsId(): array
    {
        $ids = [];

        foreach ($this->config->getOptions() as $toolId => $options) {
            if (\is_int($toolId)) {
                continue;
            }

            $ids[] = $toolId;
        }

        return $ids;
    }
}
