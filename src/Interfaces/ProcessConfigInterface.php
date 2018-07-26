<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ProcessConfigInterface
{
    /**
     * Configure the current instance.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function processConfig(ConfigInterface $config): void;
}
