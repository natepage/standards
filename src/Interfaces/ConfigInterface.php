<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ConfigInterface
{
    /**
     * Get all config.
     *
     * @return mixed[]
     */
    public function all(): array;

    /**
     * Get all config in a flattened array.
     *
     * @return mixed[]
     */
    public function allFlat(): array;

    /**
     * Get config value for given key, fallback to given default if not found.
     *
     * @param string $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Merge current config with given one.
     *
     * @param mixed[] $config
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function merge(array $config): self;

    /**
     * Set config value for given key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function set(string $key, $value): self;
}
