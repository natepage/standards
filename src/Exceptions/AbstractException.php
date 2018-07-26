<?php
declare(strict_types=1);

namespace NatePage\Standards\Exceptions;

use Exception;
use NatePage\Standards\Interfaces\StandardsExceptionInterface;

abstract class AbstractException extends Exception implements StandardsExceptionInterface
{
    // No body needed
}
