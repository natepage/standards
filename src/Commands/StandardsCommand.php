<?php
declare(strict_types=1);

namespace NatePage\Standards\Commands;

use NatePage\Standards\Configs\Config;
use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Helpers\DefaultToolsCollectionHelper;
use NatePage\Standards\Helpers\StandardsConfigHelper;
use NatePage\Standards\Helpers\StandardsOutputHelper;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ToolsAwareInterface;
use NatePage\Standards\Interfaces\ToolsCollectionInterface;
use NatePage\Standards\Interfaces\ToolsRunnerInterface;
use NatePage\Standards\Runners\ToolsRunner;
use NatePage\Standards\Traits\ToolsAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StandardsCommand extends Command implements ToolsAwareInterface
{
    use ToolsAwareTrait;

    public const EXIT_CODE_ERROR = 1;
    public const EXIT_CODE_SUCCESS = 0;

    /**
     * @var \NatePage\Standards\Interfaces\ConfigInterface
     */
    private $config;

    /**
     * StandardsCommand constructor.
     *
     * @param null|\NatePage\Standards\Interfaces\ConfigInterface $config
     * @param null|\NatePage\Standards\Interfaces\ToolsCollectionInterface $tools
     */
    public function __construct(?ConfigInterface $config = null, ?ToolsCollectionInterface $tools = null)
    {
        // Define package base path as constant to be used in YAML config
        \define('NPS_BASE_PATH', __DIR__ . '/../../');

        $this->config = $config ?? new Config();
        $this->tools = $tools ?? (new DefaultToolsCollectionHelper())->getTools();

        parent::__construct();
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
            ->setName('standards')
            ->setDescription('Check the code against the coding standards');

        // Add global options to config
        $this->config->addOptions([
            new ConfigOption('display-config', false, 'Display config'),
            new ConfigOption('only', null, 'Run only specified tools'),
            new ConfigOption('paths', 'app,src,tests', 'Specify the paths to run the tools on'),
            new ConfigOption('exit-on-failure', false, 'Exit on failure on not')
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
        foreach ($this->config->getOptions() as $tool => $options) {
            /**
             * @var \NatePage\Standards\Interfaces\ConfigOptionInterface $option
             */
            foreach ($options as $option) {
                if ($option->isExposed() === false) {
                    continue;
                }

                $key = \is_int($tool) ? $option->getName() : \sprintf('%s.%s', $tool, $option->getName());

                $this->addOption(
                    $key,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    $option->getDescription(),
                    $option->getDefault()
                );
            }
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

        // Otherwise it's successful, render champion message and return success exit code
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

        $toolsRunner->setConfig($this->config);
        $toolsRunner->setInput($input);
        $toolsRunner->setOutput($output);

        return $toolsRunner;
    }
}
