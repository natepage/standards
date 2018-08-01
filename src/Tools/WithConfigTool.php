<?php
declare(strict_types=1);

namespace NatePage\Standards\Tools;

use NatePage\Standards\Configs\ConfigOption;
use NatePage\Standards\Interfaces\ConfigAwareInterface;
use NatePage\Standards\Interfaces\ConfigInterface;
use NatePage\Standards\Traits\ConfigAwareTrait;
use Symfony\Component\Yaml\Yaml;

/** @noinspection LowerAccessLevelInspection $options is used by the children tools */

abstract class WithConfigTool extends AbstractTool implements ConfigAwareInterface
{
    use ConfigAwareTrait {
        setConfig as private traitSetConfig;
    }

    /**
     * @var mixed[]
     */
    protected static $options;

    /**
     * Set config.
     *
     * @param \NatePage\Standards\Interfaces\ConfigInterface $config
     *
     * @return void
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->traitSetConfig($config);

        // Set enabled option
        $config->addOption(new ConfigOption(
            'enabled',
            true,
            \sprintf('Whether or not to run %s', $this->getId())
        ), $this->getId());

        $options = $this->getOptions();
        if (isset($options[$this->getId()]) === false) {
            return;
        }

        foreach ($options[$this->getId()] as $name => $attributes) {
            if (\is_array($attributes) === false) {
                $config->addOption(new ConfigOption($name, $attributes), $this->getId());

                continue;
            }

            $config->addOption(new ConfigOption(
                $name,
                $attributes['default'] ?? null,
                $attributes['description'] ?? null,
                $attributes['exposed'] ?? null
            ), $this->getId());
        }
    }

    /**
     * Get options from config/tools.yaml, return empty array if file doesn't exist.
     *
     * @return mixed[]
     */
    private function getOptions(): array
    {
        if (static::$options !== null) {
            return static::$options;
        }

        $configTools = __DIR__ . '/../../config/tools.yaml';

        if (\file_exists($configTools) === false) {
            return [];
        }

        $loaded = Yaml::parse(\file_get_contents($configTools), Yaml::PARSE_CONSTANT) ?? [];

        // Resolve parameters
        if (empty($loaded['parameters'] ?? null) === false) {
            $loaded = $this->resolveParameters($loaded, $loaded['parameters']);
        }

        return static::$options = $loaded;
    }

    /**
     * Resolve parameters in strings.
     *
     * @param mixed[] $options
     * @param mixed[] $parameters
     *
     * @return mixed[]
     */
    private function resolveParameters(array $options, array $parameters): array
    {
        foreach ($options as $name => $attributes) {
            if ($name === 'parameters') {
                continue;
            }

            if (\is_array($attributes)) {
                $options[$name] = $this->resolveParameters($attributes, $parameters);

                continue;
            }

            if (\is_string($attributes) === false) {
                continue;
            }

            if (\preg_match_all('/%([\w.]+)%/', $attributes, $matches)) {
                $params = $matches[1] ?? [];
                $replace = [];

                foreach ($params as $param) {
                    if (\array_key_exists($param, $parameters) === false) {
                        continue;
                    }

                    $replace[\sprintf('%%%s%%', $param)] = $parameters[$param];
                }

                $options[$name] = \strtr($options[$name], $replace);
            }
        }

        return $options;
    }
}
