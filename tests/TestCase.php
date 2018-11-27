<?php
declare(strict_types=1);

namespace NatePage\Standards;

use Closure;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class TestCase extends PHPUnitTestCase
{
    /**
     * Convert protected/private method to public.
     *
     * @param string $className
     * @param string $methodName
     *
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException
     */
    protected function getMethodAsPublic(string $className, string $methodName): ReflectionMethod
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Convert protected/private property to public.
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return \ReflectionProperty
     *
     * @throws \ReflectionException
     */
    protected function getPropertyAsPublic(string $className, string $propertyName): ReflectionProperty
    {
        $class = new ReflectionClass($className);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Create mock configured in a closure
     *
     * @param string $classToMock
     * @param null|\Closure $closure
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    protected function mock(string $classToMock, ?Closure $closure = null): MockInterface
    {
        $mock = Mockery::mock($classToMock);

        if ($closure === null) {
            return $mock;
        }

        $closure($mock);

        return $mock;
    }
}
