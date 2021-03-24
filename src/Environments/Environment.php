<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Environments;

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
}
