<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Console\Outputs\ConsoleSectionOutput;
use NatePage\Standards\Interfaces\RunnerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParallelRunner implements RunnerInterface
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var \Symfony\Component\Process\Process[]
     */
    private $running = [];

    /**
     * @var \NatePage\Standards\Interfaces\ToolInterface[]
     */
    private $tools;

    /**
     * ParallelRunner constructor.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \NatePage\Standards\Interfaces\ToolInterface[] $tools
     */
    public function __construct(InputInterface $input, OutputInterface $output, array $tools)
    {
        $this->input = $input;
        $this->output = $output;
        $this->tools = $tools;
    }

    /**
     * Run.
     *
     * @return int
     */
    public function run(): int
    {
        $exitCode = self::EXIT_CODE_SUCCESS;

        foreach ($this->tools as $tool) {
            $output = $this->getNewOutputForTool();
            $process = $tool->getProcess();

            (new SymfonyStyle($this->input, $output))->title($tool->getName());

            $process->start($this->getProcessStartCallback($output));

            $this->running[] = $process;
        }

        while (\count($this->running)) {
            foreach ($this->running as $index => $process) {
                // If runner still running, skip
                if ($process->isRunning()) {
                    continue;
                }

                if ($process->isSuccessful() === false) {
                    $exitCode = self::EXIT_CODE_ERROR;
                }

                // Remove runner from the list of runners currently running
                unset($this->running[$index]);
            }
        }

        return $exitCode;
    }

    /**
     * Get new output for each tool.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    private function getNewOutputForTool(): OutputInterface
    {
        if ($this->output instanceof ConsoleOutputInterface) {
            return new ConsoleSectionOutput($this->output->section());
        }

        (new SymfonyStyle($this->input, $this->output))->warning(\sprintf(
            'Current output does not support sections, no guarantee about the result. 
                    Please prefer using %s for parallel runners.',
            ConsoleOutputInterface::class
        ));

        return $this->output;
    }

    /**
     * Get output callback for process.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Closure
     */
    private function getProcessStartCallback(OutputInterface $output): \Closure
    {
        return function (
            /** @noinspection PhpUnusedParameterInspection */
            $type,
            $buffer
        ) use ($output): void {
            if ($output->isVerbose() === false) {
                return;
            }

            $output->write($buffer);
        };
    }
}
