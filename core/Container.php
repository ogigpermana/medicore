<?php

namespace Core;

/**
 * Dependency Injection Container
 * Manages class dependencies and implements singleton pattern
 */

class Container
{
    private array $bindings = [];
    private array $instances = [];

    /**
     * Bind a class to a resolver
     */
    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Bind a singleton (only one instance)
     */
    public function singleton(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = function () use ($concrete, $abstract) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $concrete();
            }
            return $this->instances[$abstract];
        };
    }

    /**
     * Clear a singleton instance
     */
    public function clearInstance(string $abstract): void
    {
        unset($this->instances[$abstract]);
    }

    /**
     * Resolve a class from the container
     */
    public function get(string $abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            if (class_exists($abstract)) {
                return $this->resolve($abstract);
            }
            throw new \Exception("No binding found for {$abstract}");
        }

        return $this->bindings[$abstract]();
    }

    /**
     * Resolve a class with automatic dependency injection
     */
    private function resolve(string $class): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            if ($parameter->getType() && !$parameter->getType()->isBuiltin()) {
                $type = $parameter->getType()->getName();
                
                // If the parameter is Container, pass this instance
                if ($type === Container::class || $type === 'Core\Container' || $type === 'Core\\Container') {
                    $dependencies[] = $this;
                } else {
                    $dependencies[] = $this->get($type);
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new \Exception("Cannot resolve dependency {$parameter->getName()} in class {$class}");
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Check if a class is bound
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }

    /**
     * Check if a singleton instance exists
     */
    public function hasInstance(string $abstract): bool
    {
        return isset($this->instances[$abstract]);
    }
}