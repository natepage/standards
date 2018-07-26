<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

use Symfony\Component\Console\Command\Command;

interface CommandConfigOptionsInterface
{
    /**
     * Add options to command based on config.
     *
     * @return void
     */
    public function addOptions(): void;

    /**
     * Set command to use to add options.
     *
     * @param \Symfony\Component\Console\Command\Command $command
     *
     * @return \NatePage\Standards\Interfaces\CommandConfigOptionsInterface
     */
    public function withCommand(Command $command): self;

    /**
     * Set config to use to add options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return \NatePage\Standards\Interfaces\CommandConfigOptionsInterface
     */
    public function withConfig(ConfigInterface $config): self;
}
