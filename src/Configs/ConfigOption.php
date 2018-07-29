<?php
declare(strict_types=1);

namespace NatePage\Standards\Configs;

use NatePage\Standards\Interfaces\ConfigOptionInterface;

class ConfigOption implements ConfigOptionInterface
{
    /**
     * @var mixed[]
     */
    private $default;

    /**
     * @var bool
     */
    private $exposed;

    /**
     * @var string
     */
    private $name;

    /**
     * ConfigOption constructor.
     *
     * @param string $name
     * @param null|mixed $default
     * @param null|mixed $exposed
     */
    public function __construct(string $name, $default = null, $exposed = null)
    {
        $this->name = $name;
        $this->default = $default;
        $this->exposed = $exposed ?? true;
    }

    /**
     * Get option default value.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get option name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Check if option is exposed as runtime option.
     *
     * @return bool
     */
    public function isExposed(): bool
    {
        return $this->exposed;
    }
}
