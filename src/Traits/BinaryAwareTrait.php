<?php
declare(strict_types=1);

namespace NatePage\Standards\Traits;

use NatePage\Standards\Helpers\BinaryHelper;

trait BinaryAwareTrait
{
    /**
     * @var \NatePage\Standards\Helpers\BinaryHelper
     */
    protected $binaryHelper;

    /**
     * Set binary helper.
     *
     * @param \NatePage\Standards\Helpers\BinaryHelper $binaryHelper
     *
     * @return void
     */
    public function setBinaryHelper(BinaryHelper $binaryHelper): void
    {
        $this->binaryHelper = $binaryHelper;
    }
}