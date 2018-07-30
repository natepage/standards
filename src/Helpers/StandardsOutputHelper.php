<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ConsoleAwareInterface;
use NatePage\Standards\Traits\ConsoleAwareTrait;
use NatePage\Standards\Traits\UsesStyle;
use Symfony\Component\Console\Style\StyleInterface;

class StandardsOutputHelper implements ConsoleAwareInterface
{
    use ConsoleAwareTrait;
    use UsesStyle;

    /**
     * Write config into output if display-config enabled.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function config(ConfigInterface $config): void
    {
        if ($config->get('display-config') === false) {
            return;
        }

        $style = $this->getStyle();
        $style->section('Config');

        $rows = [];
        foreach ($config->dump() as $key => $value) {
            $rows[] = [$key, $this->toString($value)];
        }

        $style->table(['Config', 'Value'], $rows);
    }

    /**
     * Write error for given message.
     *
     * @param string $message
     *
     * @return void
     */
    public function error(string $message): void
    {
        $this->getStyle()->error($message);
    }

    /**
     * Write success for given message.
     *
     * @param string $message
     *
     * @return void
     */
    public function success(string $message): void
    {
        $this->getStyle()->success($message);
    }

    /**
     * Get output style.
     *
     * @return \Symfony\Component\Console\Style\StyleInterface
     */
    private function getStyle(): StyleInterface
    {
        return $this->style($this->input, $this->output);
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
