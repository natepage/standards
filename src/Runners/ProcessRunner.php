<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Output\ConsoleSectionOutput;
use NatePage\Standards\Traits\UsesStyle;
use Symfony\Component\Process\Process;

class ProcessRunner extends WithConsoleRunner
{
    use UsesStyle;

    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * @var string
     */
    private $title;

    /**
     * Close process.
     *
     * @return void
     */
    public function close(): void
    {
        if ($this->output->isVerbose()) {
            return;
        }

        $this->output->write(
            $this->process->isSuccessful() ? '// Successful' : $this->process->getErrorOutput()
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
     * Set process to run.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \NatePage\Standards\Runners\ProcessRunner
     */
    public function setProcess(Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return \NatePage\Standards\Runners\ProcessRunner
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

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
        $this->output = new ConsoleSectionOutput($this->output);

        if ($this->title !== null) {
            $this->output->writeln(\sprintf('<comment>%s</>', $this->title));
        }

        $this->process->start(function ($type, $buffer): void {
            if ($this->output->isVerbose() === false) {
                return;
            }

            $this->output->write($buffer);
        });
    }
}
