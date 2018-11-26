<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface RunnerInterface
{
    public const EXIT_CODE_ERROR = 1;
    public const EXIT_CODE_SUCCESS = 0;

    /**
     * Run.
     *
     * @return int
     */
    public function run(): int;
}
