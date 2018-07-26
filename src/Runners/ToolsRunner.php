<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Interfaces\ToolsRunnerInterface;
use NatePage\Standards\Traits\ToolsAwareTrait;
use NatePage\Standards\Traits\UsesStyle;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
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
     * Check if currently running.
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return empty($this->runnings) === false;
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
     * @throws \NatePage\Standards\Exceptions\MissingRequiredPropertiesException
     */
    protected function doRun(): void
    {
        $style = $this->style($this->input, $this->output);

        if ($this->tools->isEmpty()) {
            $style->error('No tools to run.');

            return;
        }

        foreach ($this->tools->all() as $tool) {
            $processRunner = new ProcessRunner();
            $processRunner->setInput($this->input);
            $processRunner->setOutput($this->getOutputForProcess($style));

            $processRunner
                ->setTitle(\sprintf('Running %s...', $tool->getName()))
                ->setProcess(new Process($tool->getCli()));

            $this->runnings[] = $processRunner->run();
        }

        while (\count($this->runnings)) {
            /**
             * @var int $index
             * @var \NatePage\Standards\Runners\ProcessRunner $processRunner
             */
            foreach ($this->runnings as $index => $processRunner) {
                if ($processRunner->isRunning()) {
                    continue;
                }

                $processRunner->close();

                unset($this->runnings[$index]);
            }
        }
    }

    /**
     * Get output for process runners.
     *
     * @param StyleInterface $style
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    private function getOutputForProcess(StyleInterface $style): OutputInterface
    {
        if ($this->output instanceof ConsoleOutputInterface) {
            return $this->output->section();
        }

        $style->warning(\sprintf(
            'Current output does not support sections, no guarantee about the result. Please prefer using %s',
            ConsoleOutputInterface::class
        ));

        return $this->output;
    }
}
