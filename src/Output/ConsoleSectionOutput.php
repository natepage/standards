<?php
declare(strict_types=1);

namespace NatePage\Standards\Output;

use Symfony\Component\Console\Output\ConsoleSectionOutput as SymfonyConsoleSectionOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleSectionOutput extends Output
{
    /**
     * @var string
     */
    private $content = '';

    /**
     * @var \Symfony\Component\Console\Output\ConsoleOutputInterface
     */
    private $output;

    /**
     * ConsoleSectionOutput constructor.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;

        parent::__construct($output->getVerbosity(), $output->isDecorated(), $output->getFormatter());
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
        if (($this->output instanceof SymfonyConsoleSectionOutput) === false) {
            $this->output->write($message, $newline);

            return;
        }

        /** @var \Symfony\Component\Console\Output\ConsoleSectionOutput $output */
        $output = $this->output;
        $output->overwrite($this->content .= $message . ($newline ? PHP_EOL : ''));
    }
}
