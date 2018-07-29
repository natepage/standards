<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Interfaces\CommandConfigOptionsInterface;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Traits\HasRequiredProperties;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class CommandConfigOptionsHelper implements CommandConfigOptionsInterface
{
    use HasRequiredProperties;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    private $command;

    /**
     * @var \NatePage\Standards\Interfaces\ConfigInterface
     */
    private $config;

    /**
     * Add options to command based on config.
     *
     * @return void
     *
     * @throws \NatePage\Standards\Exceptions\MissingRequiredPropertiesException
     */
    public function addOptions(): void
    {
        $this->requireProperties();

        foreach ($this->config->dump() as $option => $value) {
            $this->command->addOption($option, null, InputOption::VALUE_OPTIONAL, '', $value);
        }
    }

    /**
     * Set command to use to add options.
     *
     * @param \Symfony\Component\Console\Command\Command $command
     *
     * @return \NatePage\Standards\Interfaces\CommandConfigOptionsInterface
     */
    public function withCommand(Command $command): CommandConfigOptionsInterface
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Set config to use to add options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return \NatePage\Standards\Interfaces\CommandConfigOptionsInterface
     */
    public function withConfig(ConfigInterface $config): CommandConfigOptionsInterface
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Return list of required properties.
     *
     * @return string[]
     */
    protected function defineRequiredProperties(): array
    {
        return ['command', 'config'];
    }
}
