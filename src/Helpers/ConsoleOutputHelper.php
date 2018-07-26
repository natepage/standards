<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputHelper extends Output
{
    /**
     * @var string
     */
    private $content = '';

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * Set output.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \NatePage\Standards\Helpers\ConsoleOutputHelper
     */
    public function setOutput(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param bool $newline Whether to add a newline or not
     *
     * @return void
     */
    protected function doWrite($message, $newline): void
    {
        if (($this->output instanceof ConsoleSectionOutput) === false) {
            $this->output->write($message, $newline);

            return;
        }

        /** @var \Symfony\Component\Console\Output\ConsoleSectionOutput $output */
        $output = $this->output;
        $output->overwrite($this->content .= $message . ($newline ? PHP_EOL : ''));
    }
}
