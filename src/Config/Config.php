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
     * @param string      $envName
     * @param string      $expected
     * @param string|null $default
     *
     * @return bool
     */
    public function is(string $envName, string $expected, $default = null): bool
    {
        return $this->get($envName, $default) === $expected;
    }

    /**
     * @param string      $envName
     * @param string|null $default
     *
     * @return string|null
     */
    public function get(string $envName, $default = null): ?string
    {
        return Environment::getVar($envName, $default);
    }

    /**
     * @param string      $envName
     * @param string|null $value
     *
     * @return $this
     */
    public function set(string $envName, ?string $value): Config
    {
        Environment::setVar($envName, $value);

        return $this;
    }
}
