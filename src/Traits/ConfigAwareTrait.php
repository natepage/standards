<?php
declare(strict_types=1);

namespace NatePage\Standards\Traits;

use NatePage\Standards\Interfaces\ConfigInterface;

trait ConfigAwareTrait
{
    /**
     * @var \NatePage\Standards\Interfaces\ConfigInterface
     */
    protected $config;

    /**
     * Set config.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }
}
