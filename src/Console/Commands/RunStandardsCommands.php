<?php
declare(strict_types=1);

namespace NatePage\Standards\Console\Commands;

use NatePage\Standards\Helpers\InputDefinitionHelper;
use NatePage\Standards\Helpers\OutputHelper;
use NatePage\Standards\Helpers\ToolsHelper;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ToolsAwareInterface;
use NatePage\Standards\Runners\ParallelRunner;
use NatePage\Standards\Traits\ConfigAwareTrait;
use NatePage\Standards\Traits\ToolsAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunStandardsCommands extends Command implements ConfigAwareInterface, ToolsAwareInterface
{
    use ConfigAwareTrait;
    use ToolsAwareTrait;

    /**
     * Add tools options to input definition.
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    public function getNativeDefinition(): InputDefinition
    {
        $definition = parent::getNativeDefinition();

        (new InputDefinitionHelper())->addOptions($this->config, $definition);

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->getNativeDefinition(); // Required to add options to input definition

        return parent::run($input, $output);
    }

    /**
     * Configure command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('run')
            ->setDescription('Check the code against the coding standards');
    }

    /**
     * Run standards.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $outputHelper = new OutputHelper($input, $output);

        $this->config->mergeValues($input->getOptions());

        // Display config if required
        $outputHelper->config($this->config);

        return (new ParallelRunner(
            $input,
            $output,
            (new ToolsHelper())->getEnabledTools($this->config, $this->tools),
            (bool)$this->config->getValue('exit-on-failure')
        ))->run();
    }
}
