<?php
declare(strict_types=1);

namespace NatePage\Standards\Interfaces;

interface ToolInterface
{
    /**
     * Get command line to execute the tool.
     *
     * @return string
     */
    public function getCli(): string;

    /**
     * Get tool description.
     *
     * @return null|string
     */
    public function getDescription(): ?string;

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
}
