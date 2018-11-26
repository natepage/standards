<?php
declare(strict_types=1);

namespace NatePage\Standards\Helpers;

use NatePage\Standards\Exceptions\BinaryNotFoundException;
use Symfony\Component\Process\Process;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

class BinaryHelper
{
    /**
     * Resolve given binary or throw exception if not found.
     *
     * @param string $binary
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException If binary not found
     */
    public function resolveBinary(string $binary): string
    {
        // Try inspected project vendor
        $vendor = \sprintf('vendor/bin/%s', $binary);

        if (\file_exists($vendor)) {
            return $vendor;
        }

        // Try command line tool
        $process = new Process(\sprintf('command -v %s', $binary));
        $process->run();
        $command = $process->getOutput();

        if (empty($command) === false && $process->isSuccessful()) {
            return \trim($command);
        }

        // Fallback to local one
        $vendor = \sprintf('%s/bin/%s', VendorDirProvider::provide(), $binary);

        if (\file_exists($vendor)) {
            return $vendor;
        }

        throw new BinaryNotFoundException(\sprintf('Binary for %s not found.', $binary));
    }
}