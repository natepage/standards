<?php
declare(strict_types=1);

namespace NatePage\Standards\Traits;

use NatePage\Standards\Exceptions\MissingRequiredPropertiesException;

trait HasRequiredProperties
{
    /**
     * Return list of required properties.
     *
     * @return string[]
     */
    abstract protected function defineRequiredProperties(): array;

    /**
     * Check if all required properties are set, if not throw exception.
     *
     * @return void
     *
     * @throws \NatePage\Standards\Exceptions\MissingRequiredPropertiesException If at least one required property missing
     */
    protected function requireProperties(): void
    {
        $missing = [];

        foreach ($this->defineRequiredProperties() as $property) {
            if ($this->{$property} !== null) {
                continue;
            }

            $missing[] = $property;
        }

        if (empty($missing) === false) {
            throw new MissingRequiredPropertiesException(\sprintf(
                'Properties %s on %s are required',
                \implode(', ', $missing),
                \get_class($this)
            ));
        }
    }
}
