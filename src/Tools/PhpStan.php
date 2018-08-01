<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

class PhpStan extends WithConfigTool
{
    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return 'phpstan';
    }

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHPSTAN';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    protected function getCli(): string
    {
        $config = $this->config->dump();
        $neonFile = \file_exists('phpstan.neon') ? '-c phpstan.neon' : '';

        return \sprintf(
            '%s analyze %s %s --ansi --level %d --no-progress',
            $this->resolveBinary(),
            $this->spacePaths($config['paths']),
            $neonFile,
            $config['phpstan.reporting_level']
        );
    }
}
