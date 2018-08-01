<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Exceptions\BinaryNotFoundException;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\HasProcessRunnerInterface;
use NatePage\Standards\Interfaces\ProcessRunnerInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Interfaces\ToolsRunnerInterface;
use NatePage\Standards\Output\ConsoleSectionOutput;
use NatePage\Standards\Traits\ConfigAwareTrait;
use NatePage\Standards\Traits\ToolsAwareTrait;
use NatePage\Standards\Traits\UsesStyle;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ToolsRunner extends WithConsoleRunner implements ConfigAwareInterface, ToolsRunnerInterface
{
    use ConfigAwareTrait;
    use ToolsAwareTrait;
    use UsesStyle;

    /**
     * @var \NatePage\Standards\Runners\ProcessRunner[]
     */
    private $runnings = [];

    /**
     * @var bool
     */
    private $successful = true;

    /**
     * Check if currently running.
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return empty($this->runnings) === false;
    }

    /**
     * Check if all tools were successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * {@inheritdoc}
     */
    protected function defineRequiredProperties(): array
    {
        return \array_merge(parent::defineRequiredProperties(), ['tools']);
    }

    /**
     * Do run current instance.
     *
     * @return void
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary missing and exit-on-binary-missing true
     */
    protected function doRun(): void
    {
        $style = $this->style($this->input, $this->output);

        if ($this->tools->isEmpty()) {
            $style->error('No tools to run.');

            return;
        }

        foreach ($this->tools->all() as $tool) {
            $output = $this->getOutputForProcess();
            $output->writeln(\sprintf('<comment>%s</>', $tool->getName()));

            try {
                $processRunner = $this->getProcessRunner($tool, $output);
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (BinaryNotFoundException $exception) {
                if (((bool)$this->config->get('exit-on-binary-missing')) === true) {
                    throw $exception;
                }

                // If binary not found, warning and skip, but don't fail. This way we can require only wanted tools
                $output->write(\sprintf('<comment>[SKIP]</> %s', $exception->getMessage()));
                $output->writeln(' Please make sure you required all dependencies.');

                continue;
            }

            $this->runnings[] = $processRunner->run();
        }

        while (\count($this->runnings)) {
            /**
             * @var int $index
             * @var \NatePage\Standards\Runners\ProcessRunner $processRunner
             */
            foreach ($this->runnings as $index => $processRunner) {
                // If process still running, skip
                if ($processRunner->isRunning()) {
                    continue;
                }

                $processRunner->close();

                // If process not successful, tools runner not successful neither
                if ($processRunner->isSuccessful() === false) {
                    $this->successful = false;

                    if ((bool)$this->config->get('exit-on-failure')) {
                        return;
                    }
                }

                unset($this->runnings[$index]);
            }
        }
    }

    /**
     * Get output for process runners.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    private function getOutputForProcess(): OutputInterface
    {
        if ($this->output instanceof ConsoleOutputInterface) {
            return new ConsoleSectionOutput($this->output->section());
        }

        $this->style($this->input, $this->output)->warning(\sprintf(
            'Current output does not support sections, no guarantee about the result. Please prefer using %s',
            ConsoleOutputInterface::class
        ));

        return $this->output;
    }

    /**
     * Get process runner.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \NatePage\Standards\Interfaces\ProcessRunnerInterface
     */
    private function getProcessRunner(ToolInterface $tool, OutputInterface $output): ProcessRunnerInterface
    {
        $processRunner = $tool instanceof HasProcessRunnerInterface ? $tool->getProcessRunner() : new ProcessRunner();

        $processRunner->setInput($this->input);
        $processRunner->setOutput($output);

        return $processRunner->setProcess($tool->getProcess());
    }
}
