<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ConfigAwareInterface
{
    /**
     * Set config.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function setConfig(ConfigInterface $config): void;
}
