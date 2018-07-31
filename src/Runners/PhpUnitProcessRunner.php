<?php
declare(strict_types=1);

namespace NatePage\Standards\Runners;

use NatePage\Standards\Traits\UsesStyle;

class PhpUnitProcessRunner extends ProcessRunner
{
    use UsesStyle;

    /**
     * @var int
     */
    private $minimumCoverage;

    /**
     * PhpUnitProcessRunner constructor.
     *
     * @param int $minimumCoverage
     */
    public function __construct(int $minimumCoverage)
    {
        $this->minimumCoverage = $minimumCoverage;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful(): bool
    {
        return $this->isCoverageOk();
    }

    /**
     * Check if coverage is greater or equal than given minimum one.
     *
     * @return bool
     */
    private function isCoverageOk(): bool
    {
        if ($this->process->isRunning()) {
            return $this->process->isSuccessful();
        }

        $result = $this->process->getOutput();

        \preg_match('#lines:([0-9 .]+)%#i', $result, $matches);

        $coverage = $matches[1] ?? null;

        if ($coverage === null) {
            $this->style($this->input, $this->output)->warning(
                'Unable to check code coverage, please make sure Xdebug or PHPDBG are installed'
            );

            return true;
        }

        return $this->minimumCoverage <= (int)$coverage;
    }
}
