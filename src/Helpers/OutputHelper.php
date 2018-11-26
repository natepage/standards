<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Interfaces\ConfigInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OutputHelper
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
     * OutputHelper constructor.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Render config if display-config is true.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function config(ConfigInterface $config): void
    {
        if (($config->getValue('display-config') ?? false) === false) {
            return;
        }

        $style = new SymfonyStyle($this->input, $this->output);

        $style->section('Config');

        $excludes = ['help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction', 'config'];
        $rows = [];
        $values = $config->getAllValues();

        foreach ($values as $key => $value) {
            // Skip excluded keys
            if (\in_array($key, $excludes, true)) {
                continue;
            }

            // If not a tool config, add to table and skip
            if (\preg_match('/(\w+).\w+/i', $key, $matches) !== 1) {
                $rows[] = [$key, $this->toString($value)];

                continue;
            }

            // Skip if tool is disabled
            if (($values[\strtolower(\sprintf('%s.enabled', $matches[1]))] ?? true) === false) {
                continue;
            }

            $rows[] = [$key, $this->toString($value)];
        }

        $style->table(['Config', 'Value'], $rows);
    }

    /**
     * Convert given value to string.
     *
     * @param mixed $value
     *
     * @return string
     */
    private function toString($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string)$value;
    }
}
