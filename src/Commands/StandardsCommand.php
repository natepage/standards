<?php
declare(strict_types=1);

namespace NatePage\Standards\Commands;

use NatePage\Standards\Helpers\CommandConfigOptionsHelper;
use NatePage\Standards\Helpers\StandardsConfigHelper;
use NatePage\Standards\Helpers\StandardsOutputHelper;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ProcessConfigInterface;
use NatePage\Standards\Interfaces\ToolsAwareInterface;
use NatePage\Standards\Runners\ToolsRunner;
use NatePage\Standards\Traits\ToolsAwareTrait;
use NatePage\Standards\Traits\UsesSymfonyConfig;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StandardsCommand extends Command implements ProcessConfigInterface, ToolsAwareInterface
{
    use ToolsAwareTrait;
    use UsesSymfonyConfig {
        processConfig as private traitProcessConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function processConfig(ConfigInterface $config): void
    {
        $this->traitProcessConfig($config);

        $this
            ->configureTools()
            ->configureOptions();
    }

    /**
     * Configure command.
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('check')
            ->setDescription('Check the code against the coding standards');
    }

    /**
     * Define the config structure using the given node definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root
     *
     * @return void
     */
    protected function defineConfigStructure(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
            ->scalarNode('only')
            ->beforeNormalization()
            ->ifArray()
            ->then(function (array $value): string {
                return \implode(',', $value);
            })
            ->end()
            ->defaultValue(null)
            ->end()
            ->scalarNode('paths')
            ->beforeNormalization()
            ->ifArray()
            ->then(function (array $value): string {
                return \implode(',', $value);
            })
            ->end()
            ->defaultValue('app,src,tests')
            ->end()
            ->end();
    }

    /**
     * Define the root node name.
     *
     * @return string
     */
    protected function defineRootNodeName(): string
    {
        return 'standards';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \NatePage\Standards\Exceptions\InvalidOptionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $configHelper = $this->getConfigHelper();
        $outputHelper = $this->getOutputHelper($input, $output);

        // Update config with input options from user
        $configHelper
            ->merge($input->getOptions())
            ->normalizePaths()
            ->handleToolsState();

        // Display config if asked
        $outputHelper->config($this->config);

        $toolsRunner = new ToolsRunner();
        $toolsRunner->setInput($input);
        $toolsRunner->setOutput($output);

        // Run only enabled tools
        foreach ($this->tools->all() as $tool) {
            if ($configHelper->isToolEnabled($tool->getId()) === false) {
                continue;
            }

            $toolsRunner->addTool($tool);
        }

        try {
            $toolsRunner->run();
        } catch (\Exception $exception) {
            $outputHelper->error($exception->getMessage());
        }

        return null;
    }

    /**
     * Configure command options.
     *
     * @return self
     */
    private function configureOptions(): self
    {
        $this->addOption(
            'display-config',
            null,
            InputOption::VALUE_OPTIONAL,
            'Display the config used',
            false
        );

        (new CommandConfigOptionsHelper())
            ->withCommand($this)
            ->withConfig($this->config)
            ->addOptions();

        return $this;
    }

    /**
     * Configure tools.
     *
     * @return self
     */
    private function configureTools(): self
    {
        foreach ($this->tools->all() as $tool) {
            if (($tool instanceof ProcessConfigInterface) === false) {
                continue;
            }

            /** @var \NatePage\Standards\Interfaces\ProcessConfigInterface $tool */
            $tool->processConfig($this->config);
        }

        return $this;
    }

    /**
     * Get config helper.
     *
     * @return \NatePage\Standards\Helpers\StandardsConfigHelper
     */
    private function getConfigHelper(): StandardsConfigHelper
    {
        $configHelper = new StandardsConfigHelper();
        $configHelper->setConfig($this->config);

        return $configHelper;
    }

    /**
     * Get output helper.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \NatePage\Standards\Helpers\StandardsOutputHelper
     */
    private function getOutputHelper(InputInterface $input, OutputInterface $output): StandardsOutputHelper
    {
        $outputHelper = new StandardsOutputHelper();
        $outputHelper->setInput($input);
        $outputHelper->setOutput($output);

        return $outputHelper;
    }
}
