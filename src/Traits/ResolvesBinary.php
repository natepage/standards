<?php
declare(strict_types=1);

namespace NatePage\Standards\Traits;

use NatePage\Standards\Exceptions\BinaryNotFoundException;
use Symfony\Component\Process\Process;

trait ResolvesBinary
{
    /**
     * Resolve given binary or return null.
     *
     * @param null|string $binary
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary not found
     */
    protected function resolveBinary(string $binary): string
    {
        $vendor = \sprintf('vendor/bin/%s', $binary);

        if (\file_exists($vendor)) {
            return $vendor;
        }

        $process = new Process(\sprintf('command -v %s', $binary));
        $process->run();
        $command = $process->getOutput();

        if (empty($command) === false && $process->isSuccessful()) {
            return $command;
        }

        throw new BinaryNotFoundException(\sprintf('Binary for %s not found.', $binary));
    }
}
