<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

use NatePage\Standards\Helpers\BinaryHelper;

interface BinaryAwareInterface
{
    /**
     * Set binary helper.
     *
     * @param \NatePage\Standards\Helpers\BinaryHelper $binaryHelper
     *
     * @return void
     */
    public function setBinaryHelper(BinaryHelper $binaryHelper): void;
}