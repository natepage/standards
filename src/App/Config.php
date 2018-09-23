<?php
declare(strict_types=1);

namespace NatePage\Standards\App;

use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Interfaces\ToolInterface;
use NatePage\Standards\Interfaces\ToolsAwareInterface;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

class Config implements ConfigInterface, ToolsAwareInterface
{
    /**
     * @var mixed[]
     */
    private $options = [];

    /**
     * @var mixed[]
     */
    private $values = [];

    /**
     * Config constructor.
     *
     * @param \Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider
     */
    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->initOptions($parameterProvider);
    }

    /**
     * Add tool.
     *
     * @param \NatePage\Standards\Interfaces\ToolInterface $tool
     *
     * @return void
     */
    public function addTool(ToolInterface $tool): void
    {
        $options = $tool->getOptions();
        $options['enabled'] = ['default' => true, 'description' => \sprintf('Enable %s', $tool->getName())];

        foreach ($options as $name => $option) {
            $key = \strtolower(\sprintf('%s.%s', $tool->getName(), $name));

            $this->options[$key] = $option;

            // If value already set in config file, skip
            if (isset($this->values[$key])) {
                continue;
            }

            $this->values[$key] = $option['default'] ?? null;
        }
    }

    /**
     * Get all options.
     *
     * @return mixed[]
     */
    public function getAllOptions(): array
    {
        return $this->options;
    }

    /**
     * Get all values.
     *
     * @return mixed[]
     */
    public function getAllValues(): array
    {
        return $this->values;
    }

    /**
     * Get config option for given name.
     *
     * @param string $name
     *
     * @return null|mixed
     */
    public function getOption(string $name): ?array
    {
        return $this->options[$name] ?? null;
    }

    /**
     * Get config value for given option.
     *
     * @param string $option
     *
     * @return mixed
     */
    public function getValue(string $option)
    {
        return $this->values[$option] ?? null;
    }

    /**
     * Merge current values with given ones.
     *
     * @param mixed[] $values
     *
     * @return void
     */
    public function mergeValues(array $values): void
    {
        foreach ($values as $name => $value) {
            if (\is_bool($this->options[$name]['default'] ?? null)) {
                $this->values[$name] = $this->getBoolValue($name, $value);

                continue;
            }

            $this->values[$name] = $value;
        }
        
        // Might find a better way to handle this kind of things
        $this->handleInvalidPaths();
        $this->handleOnlyTools();
    }

    /**
     * Get bool value for given config key and value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    private function getBoolValue(string $key, $value): bool
    {
        return \array_key_exists($key, $this->values) && $value !== false && $value !== 'false';
    }

    /**
     * Remove invalid paths from paths config value.
     *
     * @return void
     */
    private function handleInvalidPaths(): void
    {
        if (empty($this->getValue('paths'))) {
            return;
        }

        // Remove paths which don't exist in cwd
        $paths = [];
        foreach (\explode(',', $this->getValue('paths') ?? '') as $path) {
            if (\is_dir($path) === false && \file_exists($path) === false) {
                continue;
            }

            $paths[] = $path;
        }

        $this->values['paths'] = \implode(',', $paths);
    }

    /**
     * Enable only given tools.
     *
     * @return void
     */
    private function handleOnlyTools(): void
    {
        if (empty($this->getValue('only'))) {
            return;
        }

        // Disable all tools
        foreach ($this->values as $name => $value) {
            if (\preg_match('/(\w+).enabled/i', $name) !== 1) {
                continue;
            }

            $this->values[$name] = false;
        }

        // Enable only given tools
        foreach (\explode(',', $this->getValue('only')) as $only) {
            $this->values[\strtolower(\sprintf('%s.enabled', $only))] = true;
        }
    }

    /**
     * Initialise config options.
     *
     * @param \Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider
     *
     * @return void
     */
    private function initOptions(ParameterProvider $parameterProvider): void
    {
        // Global options
        foreach ($parameterProvider->provideParameter('standards') ?? [] as $name => $option) {
            $this->options[$name] = $option;
            $this->values[$name] = $option['default'] ?? null;
        }

        // Tools options
        foreach ($parameterProvider->provideParameter('tools') ?? [] as $tool => $options) {
            foreach ((array)$options as $name => $option) {
                $key = \strtolower(\sprintf('%s.%s', $tool, $name));

                $this->values[$key] = $option['default'] ?? null;
            }
        }
    }
}
