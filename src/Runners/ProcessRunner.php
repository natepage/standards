<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Interfaces\ProcessInterface;
use NatePage\Standards\Interfaces\ProcessRunnerInterface;

class ProcessRunner extends WithConsoleRunner implements ProcessRunnerInterface
{
    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * Close process.
     *
     * @return void
     */
    public function close(): void
    {
        $output = \trim($this->process->getOutput());

        if ($this->output->isVerbose()) {
            if (empty($output)) {
                $this->output->write('// Successful');
            }

            return;
        }

        $this->output->write(
            $this->isSuccessful() ? '// Successful' : $this->process->getOutput()
        );
    }

    /**
     * Check if process is running.
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    /**
     * Check if process is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->process->isSuccessful();
    }

    /**
     * Set process to run.
     *
     * @param \NatePage\Standards\Interfaces\ProcessInterface
     *
     * @return \NatePage\Standards\Interfaces\ProcessRunnerInterface
     */
    public function setProcess(ProcessInterface $process): ProcessRunnerInterface
    {
        $this->process = $process;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function defineRequiredProperties(): array
    {
        return \array_merge(parent::defineRequiredProperties(), ['process']);
    }

    /**
     * Do run current instance.
     *
     * @return void
     */
    protected function doRun(): void
    {
        $this->process->start(function (
            /** @noinspection PhpUnusedParameterInspection */
            $type,
            $buffer
        ): void {
            if ($this->output->isVerbose() === false) {
                return;
            }

            $this->output->write($buffer);
        });
    }
}
