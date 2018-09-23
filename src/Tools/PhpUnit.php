<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use EoneoPay\Utils\XmlConverter;
use NatePage\Standards\Exceptions\BinaryNotFoundException;
use NatePage\Standards\Processes\PhpUnitProcess;
use Symfony\Component\Process\Process;

class PhpUnit extends AbstractTool
{
    private const AUTOLOAD = 'vendor/autoload.php';

    /**
     * Get tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PHPUNIT';
    }

    /**
     * Get tool options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return [
            'config-file' => [
                'default' => 'phpunit.xml',
                'description' => 'Config file to use to run PHPUnit'
            ],
            'coverage-minimum-level' => [
                'default' => 90,
                'description' => 'The minimum coverage to have, will be ignored if coverage check is disabled'
            ],
            'enable-code-coverage' => [
                'default' => true,
                'description' => 'Whether or not to enable code coverage checks'
            ],
            'junit-log-path' => [
                'description' => 'The path to output junit parseable log file, will be ignored if left blank'
            ],
            'paratest-processes-number' => [
                'default' => 8,
                'description' => 'Number of processes to run when using paratest'
            ],
            'tests-directory' => [
                'default' => 'tests',
                'description' => 'The tests directory, will be ignored it phpunit.xml exists in working directory'
            ]
        ];
    }

    /**
     * Get process to run.
     *
     * @return \Symfony\Component\Process\Process
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    public function getProcess(): Process
    {
        // Get base cli
        $cli = $this->preferParatest() ?? $this->preferPhpunit();

        // If config file doesn't exist, manually set bootstrap and test directory
        if (\file_exists($this->getOptionValue('config-file') ?? '') === false) {
            $cli .= \sprintf(' --bootstrap=%s %s', self::AUTOLOAD, $this->getOptionValue('tests-directory'));
        }

        // If coverage enabled
        if ($this->getOptionValue('enable-code-coverage') === true) {
            $cli = $this->withCoverage($cli);

            // If junit enabled
            if (empty($this->getOptionValue('junit-log-path')) === false) {
                $cli .= \sprintf(' --log-junit=%s', $this->getOptionValue('junit-log-path'));
            }
        }

        return new PhpUnitProcess(
            $cli,
            $this->getOptionValue('coverage-minimum-level'),
            $this->getEnvValues()
        );
    }

    /**
     * Get env values from config files.
     *
     * @return mixed[]
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    private function getEnvValues(): array
    {
        $configFile = $this->getOptionValue('config-file') ?? '';

        if (\file_exists($configFile) === false) {
            return [];
        }

        $env = [];

        $phpUnitConfig = (new XmlConverter())->xmlToArray(
            \file_get_contents($configFile),
            XmlConverter::XML_INCLUDE_ATTRIBUTES
        );

        foreach ($phpUnitConfig['php']['env'] ?? [] as $node) {
            $name = $node['@attributes']['name'] ?? null;

            if ($name === null) {
                continue;
            }

            $env[$name] = $node['@attributes']['value'] ?? 'NULL';
        }

        return $env;
    }

    /**
     * If paratest is available then return cli else null.
     *
     * @return null|string
     */
    private function preferParatest(): ?string
    {
        try {
            $binary = $this->resolveBinary('paratest');
        } catch (BinaryNotFoundException $exception) {
            return null;
        }

        return \sprintf(
            '%s -p%d --runner=WrapperRunner --colors',
            $binary,
            (int)$this->getOptionValue('paratest-processes-number')
        );
    }

    /**
     * If phpunit is available then return cli else fail.
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    private function preferPhpunit(): string
    {
        return \sprintf('%s --colors=always', $this->resolveBinary());
    }

    /**
     * Get cli with coverage.
     *
     * @param string $cli
     *
     * @return string
     */
    private function withCoverage(string $cli): string
    {
        try {
            $binary = $this->resolveBinary('phpdbg');
        } catch (BinaryNotFoundException $exception) {
            return \sprintf('%s --coverage-text', $cli);
        }

        return \sprintf('%s -qrr %s --coverage-text', $binary, $cli);
    }
}
