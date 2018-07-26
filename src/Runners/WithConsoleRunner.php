<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Interfaces\ConsoleAwareInterface;
use NatePage\Standards\Interfaces\RunnerInterface;
use NatePage\Standards\Traits\ConsoleAwareTrait;
use NatePage\Standards\Traits\HasRequiredProperties;

abstract class WithConsoleRunner implements ConsoleAwareInterface, RunnerInterface
{
    use ConsoleAwareTrait;
    use HasRequiredProperties;

    /**
     * Run given instance.
     *
     * @return \NatePage\Standards\Interfaces\RunnerInterface
     *
     * @throws \NatePage\Standards\Exceptions\MissingRequiredPropertiesException
     */
    public function run(): RunnerInterface
    {
        $this->requireProperties();
        $this->doRun();

        return $this;
    }

    /**
     * Do run current instance.
     *
     * @return void
     */
    abstract protected function doRun(): void;

    /**
     * Return list of required properties.
     *
     * @return string[]
     */
    protected function defineRequiredProperties(): array
    {
        return ['input', 'output'];
    }
}
