<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Interfaces\ProcessConfigInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Traits\UsesSymfonyConfig;
use NatePage\Standards\Traits\ResolvesBinary;

abstract class WithSymfonyProcessConfigTool implements ProcessConfigInterface, ToolInterface
{
    use UsesSymfonyConfig;
    use ResolvesBinary {
        resolveBinary as private traitResolveBinary;
    }

    /**
     * Get tool description.
     *
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return null;
    }

    /**
     * Define the root node name.
     *
     * @return string
     */
    protected function defineRootNodeName(): string
    {
        return $this->getId();
    }

    /**
     * Resolve binary using id if not provided.
     *
     * @param null|string $binary
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    protected function resolveBinary(?string $binary = null): string
    {
        return $this->traitResolveBinary($binary ?? $this->getId());
    }

    /**
     * Return paths separated by spaces instead of commas.
     *
     * @param string $paths
     *
     * @return string
     */
    protected function spacePaths(string $paths): string
    {
        return \str_replace(',', ' ', $paths);
    }
}
