<?php
declare(strict_types=1);

namespace NatePage\Standards;

use EoneoPay\Utils\Arr;
use NatePage\Standards\Interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @var mixed[]
     */
    private $config;

    /**
     * @var mixed[]
     */
    private $flattened;

    /**
     * Config constructor.
     *
     * @param mixed[]|null $config
     */
    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];
        $this->flattened = $this->flatten($this->config);
    }

    /**
     * Get all config.
     *
     * @return mixed[]
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Get all config in a flattened array.
     *
     * @return mixed[]
     */
    public function allFlat(): array
    {
        return $this->flattened;
    }

    /**
     * Get config value for given key, fallback to given default if not found.
     *
     * @param string $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->flattened[$key] ?? $default;
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
        $this->flattened = \array_merge($this->flattened, $config);
        $this->config = (new Arr())->unflatten($this->flattened);

        return $this;
    }

    /**
     * Set config value for given key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function set(string $key, $value): ConfigInterface
    {
        $config = $this->config;

        (new Arr())->set($config, $key, $value);

        $this->config = $config;
        $this->flattened = $this->flatten($this->config);

        return $this;
    }

    /**
     * Flatten an array into dot notation
     *
     * @param mixed[] $array The array to flatten
     * @param string|null $prepend The flattened array key so far
     *
     * @return mixed[]
     */
    private function flatten(array $array, ?string $prepend = null): array
    {
        $flattened = [];

        foreach ($array as $key => $value) {
            // If value is an array, recurse
            if (\is_array($value) && \count($value)) {
                $flattened[] = $this->flatten($value, \sprintf('%s%s.', (string)$prepend, $key));
                continue;
            }

            // Set value
            $flattened[] = [\sprintf('%s%s', (string)$prepend, $key) => $value];
        }

        // Merge flattened keys if some were found otherwise return an empty array
        return \count($flattened) ? \array_merge(...$flattened) : [];
    }
}
