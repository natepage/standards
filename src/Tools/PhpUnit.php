<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Exceptions\BinaryNotFoundException;
use NatePage\Standards\Exceptions\UnableToRunToolException;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\HasProcessRunnerInterface;
use NatePage\Standards\Interfaces\ProcessRunnerInterface;
use NatePage\Standards\Runners\PhpUnitProcessRunner;
use NatePage\Standards\Runners\ProcessRunner;

class PhpUnit extends WithConfigTool implements HasProcessRunnerInterface
{
    private const AUTOLOAD = 'vendor/autoload.php';

    /**
     * Get command line to execute the tool.
     *
     * @return string
     *
     * @throws \NatePage\Standards\Exceptions\UnableToRunToolException
     * @throws \NatePage\Standards\Exceptions\BinaryNotFoundException
     */
    public function getCli(): string
    {
        $config = $this->config->dump();

        // Check minimum requirements
        $this->checkMinRequirements($config);

        // Get base cli
        $cli = $this->preferParatest() ?? $this->preferPhpunit();

        // If config file doesn't exist, manually set bootstrap and test directory
        if (\file_exists($config['phpunit.config_file']) === false) {
            $cli .= \sprintf(' --bootstrap=%s %s', self::AUTOLOAD, $config['phpunit.test_directory']);
        }

        // If coverage enabled
        if ($config['phpunit.enable_code_coverage'] === true) {
            $cli = $this->withCoverage($cli);

            // If junit enabled
            if (empty($config['phpunit.junit_log_path']) === false) {
                $cli .= \sprintf(' --log-junit=%s', $config['phpunit.junit_log_path']);
            }
        }

        return $cli;
    }

    /**
     * Get tool identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return 'phpunit';
    }

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
     * Get process runner.
     *
     * @return \NatePage\Standards\Interfaces\ProcessRunnerInterface
     */
    public function getProcessRunner(): ProcessRunnerInterface
    {
        if ($this->config->get('phpunit.enable_code_coverage') === false) {
            return new ProcessRunner();
        }

        return new PhpUnitProcessRunner((int)$this->config->get('phpunit.coverage_minimum_level'));
    }

    /**
     * Define tool options.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    protected function defineOptions(ConfigInterface $config): void
    {
        $config->addOptions([
            new ConfigOption(
                'config_file',
                'phpunit.xml',
                'Config file to use to run PHPUnit'
            ),
            new ConfigOption(
                'coverage_minimum_level',
                90,
                'The minimum percentage of coverage to have, will be ignored if coverage check is disabled'
            ),
            new ConfigOption(
                'enable_code_coverage',
                true,
                'Whether or not to enable code coverage checks'
            ),
            new ConfigOption(
                'junit_log_path',
                null,
                'The path to output junit parseable log file, can be relative, will be ignored if left blank'
            ),
            new ConfigOption(
                'test_directory',
                'tests',
                'The directory containing tests, will be ignored it phpunit.xml exists in working directory'
            )
        ], $this->getId());
    }

    /**
     * Check if minimum requirements are here.
     *
     * @param mixed[] $config
     *
     * @return bool
     *
     * @throws \NatePage\Standards\Exceptions\UnableToRunToolException
     */
    private function checkMinRequirements(array $config): bool
    {
        if (\file_exists($config['phpunit.config_file']) === false
            && (\file_exists(self::AUTOLOAD) === false || \is_dir($config['phpunit.test_directory']))) {
            throw new UnableToRunToolException(\sprintf(
                'Unable to run phpunit as %s cannot be loaded and %s or %s is missing',
                $config['phpunit.config_file'],
                self::AUTOLOAD,
                $config['phpunit.test_directory']
            ));
        }

        return true;
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

        return \sprintf('%s -p8 --runner=WrapperRunner --colors', $binary);
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
