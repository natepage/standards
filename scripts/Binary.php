<?php
declare(strict_types=1);

namespace Scripts\NatePage\Standards;

use Symfony\Component\Process\Process;

class Binary
{
    /**
     * Install standards binary in local bin directory.
     *
     * @return void
     */
    public static function installStandards(): void
    {
        static::runProcess(\sprintf('ln -s %s/../bin/standards /usr/local/bin/standards', __DIR__));
    }

    /**
     * Run process and echo output.
     *
     * @param string $cmd
     *
     * @return void
     */
    private static function runProcess(string $cmd): void
    {
        $process = new Process($cmd);
        $process->run();

        echo $process->isSuccessful() ? $process->getOutput() : $process->getErrorOutput();
    }
}
