<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;

class EnvReader
{
    /**
     * @return bool
     */
    public function isStaging(): bool
    {
        return Environment::getVar(Env::ENV_NAME) === Environment::STAGING;
    }

    /**
     * @return bool
     */
    public function isTesting(): bool
    {
        return Environment::getVar(Env::ENV_NAME) === Environment::TESTING;
    }

    /**
     * @return bool
     */
    public function isDevelopment(): bool
    {
        return Environment::getVar(Env::ENV_NAME) === Environment::DEVELOPMENT;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return Environment::getVar(Env::ENV_NAME) === Environment::PRODUCTION;
    }

    /**
     * @param string      $name
     * @param string      $expected
     * @param string|null $default
     *
     * @return bool
     */
    public function is(string $name, string $expected, ?string $default = null): bool
    {
        return $this->get($name, $default) === $expected;
    }

    /**
     * @param string      $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function get(string $name, ?string $default = null): ?string
    {
        $value = Environment::getVar($name, $default);
        if (null === $value || false === $value) {
            return null;
        }

        return (string)$value;
    }

    /**
     * @param string      $name
     * @param string|null $value
     *
     * @return $this
     * @deprecated Don't use this method without understanding consequences! No removal is planned.
     */
    public function set(string $name, ?string $value): EnvReader
    {
        putenv("$name=$value");
        $_ENV[$name]    = $value;
        $_SERVER[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     * @deprecated Don't use this method without understanding consequences! No removal is planned.
     */
    public function clear(string $name): EnvReader
    {
        putenv("$name");
        unset($_ENV[$name]);
        unset($_SERVER[$name]);

        return $this;
    }
}
