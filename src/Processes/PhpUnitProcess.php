<?php
declare(strict_types=1);

namespace NatePage\Standards\Processes;

use Symfony\Component\Process\Process;

class PhpUnitProcess extends Process
{
    /**
     * @var int
     */
    private $minimumCoverage;

    /**
     * PhpUnitProcess constructor.
     *
     * @param string|string[] $commandline
     * @param int $minimumCoverage
     * @param null|mixed[] $env
     */
    public function __construct($commandline, int $minimumCoverage, ?array $env = null)
    {
        $this->minimumCoverage = $minimumCoverage;

        parent::__construct($commandline, null, $env);
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful(): bool
    {
        $parentSuccessful = parent::isSuccessful();

        if ($parentSuccessful === false) {
            return false;
        }

        $result = $this->getOutput();

        \preg_match('#lines:([0-9 .]+)%#i', $result, $matches);

        $coverage = $matches[1] ?? null;

        if ($coverage === null) {
            $this->addOutput(
                'Unable to check code coverage, please make sure Xdebug or PHPDBG are installed'
            );

            return true;
        }

        return $this->minimumCoverage <= (int)$coverage;
    }
}
