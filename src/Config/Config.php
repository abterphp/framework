<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;

class Config
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
     * @param string $envName
     * @param mixed  $expected
     * @param mixed  $default
     *
     * @return bool
     */
    public function is(string $envName, $expected, $default = null): bool
    {
        return $this->get($envName, $default) === $expected;
    }

    /**
     * @param string $envName
     * @param mixed  $default
     *
     * @return string
     */
    public function get(string $envName, $default = null): string
    {
        return Environment::getVar($envName, $default);
    }

    /**
     * @param string $envName
     * @param mixed  $value
     *
     * @return string
     */
    public function set(string $envName, string $value): Provider
    {
        Environment::setVar($envName, $value);
    }
}
