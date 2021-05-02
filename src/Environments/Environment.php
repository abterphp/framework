<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Environments;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment as OpulenceEnvironment;

class Environment extends OpulenceEnvironment
{
    /**
     * Unsets an environment variable
     *
     * @param string $name The name of the environment variable to unset
     */
    public static function unsetVar(string $name): void
    {
        putenv("$name");
        if (array_key_exists($name, $_ENV)) {
            unset($_ENV[$name]);
        }
        if (array_key_exists($name, $_SERVER)) {
            unset($_SERVER[$name]);
        }
    }

    /**
     * Gets the value of an environment variable
     * Throws a runtime exception instead of returning null
     *
     * @param string $name The name of the environment variable to get
     * @param string $default The default value if none existed
     * @return string The value of the environment value if one was set, value of default otherwise
     */
    public static function mustGetVar(string $name, string $default = ""): string
    {
        $value = parent::getVar($name, $default);

        if (!$value) {
            throw new \RuntimeException("missing environment variable: " . $name);
        }

        return $value;
    }

    /**
     * Sets an environment variable, but does not overwrite existing variables
     *
     * @param string $name The name of the environment variable to set
     * @param mixed $value The value
     */
    public static function setVar(string $name, $value)
    {
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }

    /**
     * @return bool
     */
    public static function isStaging(): bool
    {
        return static::mustGetVar(Env::ENV_NAME) === Environment::STAGING;
    }

    /**
     * @return bool
     */
    public static function isTesting(): bool
    {
        return static::mustGetVar(Env::ENV_NAME) === Environment::TESTING;
    }

    /**
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        return static::mustGetVar(Env::ENV_NAME) === Environment::DEVELOPMENT;
    }

    /**
     * @return bool
     */
    public static function isProduction(): bool
    {
        return static::getVar(Env::ENV_NAME) === Environment::PRODUCTION;
    }
}
