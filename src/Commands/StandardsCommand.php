<?php
declare(strict_types=1);

namespace NatePage\Standards\Commands;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Helpers\StandardsConfigHelper;
use NatePage\Standards\Helpers\StandardsOutputHelper;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ToolsCollectionInterface;
use NatePage\Standards\Interfaces\ToolsRunnerInterface;
use NatePage\Standards\Runners\ToolsRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StandardsCommand extends Command
{
    public const EXIT_CODE_ERROR = 1;
    public const EXIT_CODE_SUCCESS = 0;

    /**
     * @var \NatePage\Standards\Interfaces\ConfigInterface
     */
    private $config;

    /**
     * @var \NatePage\Standards\Interfaces\ToolsCollectionInterface
     */
    private $tools;

    /**
     * StandardsCommand constructor.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     * @param \NatePage\Standards\Interfaces\ToolsCollectionInterface $tools
     * @param null|string $name
     */
    public function __construct(ConfigInterface $config, ToolsCollectionInterface $tools, ?string $name = null)
    {
        $this->config = $config;
        $this->tools = $tools;

        parent::__construct($name);
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

        // Add global options to config
        $this->config->addOptions([
            new ConfigOption('display-config', false),
            new ConfigOption('only', ''),
            new ConfigOption('paths', 'app,src,tests')
        ]);

        // Add tools options to config
        foreach ($this->tools->all() as $tool) {
            if (($tool instanceof ConfigAwareInterface) === false) {
                continue;
            }

            /** @var \NatePage\Standards\Interfaces\ConfigAwareInterface $tool */
            $tool->setConfig($this->config);
        }

        // Add config options to input options
        foreach ($this->config->dump() as $option => $value) {
            $this->addOption($option, null, InputOption::VALUE_OPTIONAL, '', $value);
        }
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
     * @throws \NatePage\Standards\Exceptions\InvalidConfigOptionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $configHelper = $this->getConfigHelper();
        $outputHelper = $this->getOutputHelper($input, $output);
        $toolsRunner = $this->getToolsRunner($input, $output);

        // Update config with input options from user
        $configHelper
            ->merge($input->getOptions())
            ->normalizePaths()
            ->handleToolsState();

        // Display config if asked
        $outputHelper->config($this->config);

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

            return self::EXIT_CODE_ERROR;
        }

        // If not successful, render error and return error exit code
        if ($toolsRunner->isSuccessful() === false) {
            $outputHelper->error('Oh you screwed up somewhere, go fix your errors');

            return self::EXIT_CODE_ERROR;
        }

        $outputHelper->success('It all looks fine to me you fucking champion!');

        return self::EXIT_CODE_SUCCESS;
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

    /**
     * Get tools runner.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \NatePage\Standards\Interfaces\ToolsRunnerInterface
     */
    private function getToolsRunner(InputInterface $input, OutputInterface $output): ToolsRunnerInterface
    {
        $toolsRunner = new ToolsRunner();

        $toolsRunner->setInput($input);
        $toolsRunner->setOutput($output);

        return $toolsRunner;
    }
}
