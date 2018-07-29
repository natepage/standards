<?php
declare(strict_types=1);

namespace NatePage\Standards\Configs;

use NatePage\Standards\Exceptions\InvalidConfigOptionException;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ConfigOptionInterface;

class Config implements ConfigInterface
{
    /**
     * @var mixed[]|null
     */
    private $cache;

    /**
     * @var mixed[]
     */
    private $config = [];

    /**
     * @var \NatePage\Standards\Interfaces\ConfigOptionInterface[]
     */
    private $options = [];

    /**
     * Add option.
     *
     * @param \NatePage\Standards\Interfaces\ConfigOptionInterface $option
     * @param null|string $tool
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function addOption(ConfigOptionInterface $option, ?string $tool = null): ConfigInterface
    {
        $index = $tool ?? 0;

        if (isset($this->options[$index]) === false) {
            $this->options[$index] = [];
        }

        $this->options[$index][] = $option;

        return $this;
    }

    /**
     * Add multiple options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigOptionInterface[] $options
     * @param null|string $tool
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function addOptions(array $options, ?string $tool = null): ConfigInterface
    {
        foreach ($options as $option) {
            $this->addOption($option, $tool);
        }

        return $this;
    }

    /**
     * Get flat representation of config.
     *
     * @return mixed[]
     */
    public function dump(): array
    {
        return $this->getCache();
    }

    /**
     * Get value for given option.
     *
     * @param string $option
     *
     * @return mixed
     *
     * @throws \NatePage\Standards\Exceptions\InvalidConfigOptionException If option doesn't exist
     */
    public function get(string $option)
    {
        $this->optionExists($option);

        return $this->getCache()[$option];
    }

    /**
     * Get options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Merge current config with given one.
     *
     * @param mixed[] $config
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function merge(array $config): ConfigInterface
    {
        $this->config = \array_merge($this->config, $config);

        return $this->invalidateCache();
    }

    /**
     * Set value for given option.
     *
     * @param string $option
     * @param mixed $value
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     *
     * @throws \NatePage\Standards\Exceptions\InvalidConfigOptionException If option doesn't exist
     */
    public function set(string $option, $value): ConfigInterface
    {
        $this->optionExists($option);

        $this->config[$option] = $value;

        return $this->invalidateCache();
    }

    /**
     * Build cache array and return it.
     *
     * @return mixed[]
     */
    private function buildCache(): array
    {
        $cache = [];

        /**
         * @var int|string $tool
         * @var \NatePage\Standards\Interfaces\ConfigOptionInterface $option
         */
        foreach ($this->options as $tool => $options) {
            foreach ($options as $option) {
                $key = \is_int($tool) ? $option->getName() : \sprintf('%s.%s', $tool, $option->getName());

                $cache[$key] = $this->config[$key] ?? $option->getDefault();
            }
        }

        return $cache;
    }

    /**
     * Get cache.
     *
     * @return mixed
     */
    private function getCache(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        return $this->cache = $this->buildCache();
    }

    /**
     * Invalidate cache.
     *
     * @return self
     */
    private function invalidateCache(): self
    {
        $this->cache = null;

        return $this;
    }

    /**
     * If given option doesn't exist, throw exception.
     *
     * @param string $option
     *
     * @return void
     *
     * @throws \NatePage\Standards\Exceptions\InvalidConfigOptionException
     */
    private function optionExists(string $option): void
    {
        if (\array_key_exists($option, $this->getCache()) === false) {
            throw new InvalidConfigOptionException(\sprintf('Config option %s does not exist', $option));
        }
    }
}
