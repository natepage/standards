<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ConfigOptionInterface
{
    /**
     * Get option default value.
     *
     * @return mixed
     */
    public function getDefault();

    /**
     * Get option name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if option is exposed as runtime option.
     *
     * @return bool
     */
    public function isExposed(): bool;
}
