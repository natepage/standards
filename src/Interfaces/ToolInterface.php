<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ToolInterface
{
    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get tool process to run.
     *
     * @return \NatePage\Standards\Interfaces\ProcessInterface
     */
    public function getProcess(): ProcessInterface;
}
