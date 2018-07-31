<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ConfigInterface
{
    /**
     * Add option.
     *
     * @param \NatePage\Standards\Interfaces\ConfigOptionInterface $option
     * @param null|string $tool
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function addOption(ConfigOptionInterface $option, ?string $tool = null): self;

    /**
     * Add multiple options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigOptionInterface[] $options
     * @param null|string $tool
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function addOptions(array $options, ?string $tool = null): self;

    /**
     * Get flat representation of config.
     *
     * @return mixed[]
     */
    public function dump(): array;

    /**
     * Get value for given option.
     *
     * @param string $option
     *
     * @return mixed
     */
    public function get(string $option);

    /**
     * Get options.
     *
     * @return \NatePage\Standards\Interfaces\ConfigOptionInterface[]
     */
    public function getOptions(): array;

    /**
     * Merge current config with given one.
     *
     * @param mixed[] $config
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function merge(array $config): self;

    /**
     * Set value for given option.
     *
     * @param string $option
     * @param mixed $value
     *
     * @return \NatePage\Standards\Interfaces\ConfigInterface
     */
    public function set(string $option, $value): self;
}
