<?php
declare(strict_types=1);

namespace NatePage\Standards\Configs;

use NatePage\Standards\Interfaces\ConfigOptionInterface;

class ConfigOption implements ConfigOptionInterface
{
    /**
     * @var mixed
     */
    private $default;

    /**
     * @var string
     */
    private $description;

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
     * @param null $default
     * @param null|string $description
     * @param null|bool $exposed
     */
    public function __construct(string $name, $default = null, ?string $description = null, ?bool $exposed = null)
    {
        $this->name = $name;
        $this->default = $default;
        $this->description = $description ?? '';
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
     * Get option description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
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
