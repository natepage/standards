<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Interfaces\HasProcessInterface;
use NatePage\Standards\Interfaces\HasProcessRunnerInterface;
use NatePage\Standards\Interfaces\ProcessRunnerInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Interfaces\ToolsRunnerInterface;
use NatePage\Standards\Output\ConsoleSectionOutput;
use NatePage\Standards\Traits\ToolsAwareTrait;
use NatePage\Standards\Traits\UsesStyle;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ToolsRunner extends WithConsoleRunner implements ToolsRunnerInterface
{
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
            $processRunner = $this->getProcessRunner($tool, $output);

            $output->writeln(\sprintf('<comment>%s</>', $tool->getName()));

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

                // If process not successful, tools runner not successful neither
                if ($processRunner->isSuccessful() === false) {
                    $this->successful = false;
                }

                $processRunner->close();

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

        return $processRunner->setProcess(
            $tool instanceof HasProcessInterface ? $tool->getProcess() : new Process($tool->getCli())
        );
    }
}
