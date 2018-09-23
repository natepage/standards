<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ConfigInterface
{
    /**
     * Get all options.
     *
     * @return mixed[]
     */
    public function getAllOptions(): array;

    /**
     * Get all values.
     *
     * @return mixed[]
     */
    public function getAllValues(): array;

    /**
     * Get config option for given name.
     *
     * @param string $name
     *
     * @return null|mixed
     */
    public function getOption(string $name): ?array;

    /**
     * Get config value for given option.
     *
     * @param string $option
     *
     * @return mixed
     */
    public function getValue(string $option);

    /**
     * Merge current values with given ones.
     *
     * @param mixed[] $values
     *
     * @return void
     */
    public function mergeValues(array $values): void;
}
