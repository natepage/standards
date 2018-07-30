<?php
declare(strict_types=1);

namespace NatePage\Standards;

use NatePage\Standards\Commands\StandardsCommand;
use NatePage\Standards\Configs\Config;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\KernelInterface;
use NatePage\Standards\Traits\ToolsAwareTrait;
use Symfony\Component\Console\Application;

class Kernel implements KernelInterface
{
    use ToolsAwareTrait;

    public const NAME = 'Standards';
    public const VERSION = '1.0.0';

    /**
     * @var \NatePage\Standards\Interfaces\ConfigInterface
     */
    private $config;

    /**
     * @var \Symfony\Component\Console\Application
     */
    private $console;

    /**
     * Kernel constructor.
     *
     * @param null|\NatePage\Standards\Interfaces\ConfigInterface $config
     * @param null|\Symfony\Component\Console\Application $console
     */
    public function __construct(?ConfigInterface $config = null, ?Application $console = null)
    {
        $this->config = $config ?? new Config();
        $this->console = $console ?? new Application(self::NAME, self::VERSION);
    }

    /**
     * Run the current standards instance and return exit code.
     *
     * @return int
     *
     * @throws \Exception If something went wrong
     */
    public function run(): int
    {
        $this->configure();

        return $this->console->run();
    }

    /**
     * Add check standards command.
     *
     * @return void
     */
    private function configure(): void
    {
        $command = new StandardsCommand($this->config, $this->tools);

        $this->console->add($command);
        $this->console->setDefaultCommand('check', true);
    }
}
