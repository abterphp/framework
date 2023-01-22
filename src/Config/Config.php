<?php

declare(strict_types=1);


namespace AbterPhp\Framework\Config;

use Opulence\Framework\Configuration\Config as OpulenceConfig;
use RuntimeException;

class Config extends OpulenceConfig
{
    /**
     * Gets a setting, but throws an exception if it's empty
     *
     * @param string $category The category of setting to get
     * @param string $setting  The name of the setting to get
     * @param mixed  $default  The default value if one does not exist
     *
     * @return string|int|float|bool The value of the setting
     * @throws \RuntimeException
     */
    public static function mustGet(string $category, string $setting, $default = null)
    {
        $value = parent::get($category, $setting, $default);

        if ($value === null) {
            throw new RuntimeException(sprintf("missing config: %s:%s", $category, $setting));
        }

        if (!is_scalar($value)) {
            throw new RuntimeException(
                sprintf("invalid config: %s:%s, type: %s", $category, $setting, gettype($value))
            );
        }

        return $value;
    }

    /**
     * @param string $category
     * @param string $setting
     * @param string $default
     *
     * @return string
     */
    public static function mustGetString(string $category, string $setting, string $default = ""): string
    {
        return (string)static::mustGet($category, $setting, $default);
    }

    /**
     * @param string $category
     * @param string $setting
     * @param bool   $default
     *
     * @return bool
     */
    public static function mustGetBool(string $category, string $setting, bool $default = false): bool
    {
        $value = static::mustGet($category, $setting, $default);

        if (is_bool($value)) {
            return $value;
        }

        if (in_array($value, [0, 1, '0', '1', ''], true)) {
            return (bool)$value;
        }

        if (is_string($value)) {
            $l = strtolower($value);

            if ($l === 'false') {
                return false;
            } elseif ($l === 'true') {
                return true;
            }
        }

        throw new RuntimeException(
            sprintf("non-bool config: %s:%s, type: %s, value: %s", $category, $setting, gettype($value), $value)
        );
    }

    /**
     * @param string $category
     * @param string $setting
     * @param int    $default
     *
     * @return int
     */
    public static function mustGetInt(string $category, string $setting, int $default = 0): int
    {
        $value = static::mustGet($category, $setting, $default);

        if (!is_numeric($value)) {
            throw new RuntimeException(
                sprintf("non-int config: %s:%s, type: %s, value: %s", $category, $setting, gettype($value), $value)
            );
        }

        return (int)$value;
    }

    /**
     * @param string $category
     * @param string $setting
     * @param float  $default
     *
     * @return float
     */
    public static function mustGetFloat(string $category, string $setting, float $default = 0.0): float
    {
        $value = static::mustGet($category, $setting, $default);

        if (!is_numeric($value)) {
            throw new RuntimeException(
                sprintf("non-float config: %s:%s, type: %s, value: %s", $category, $setting, gettype($value), $value)
            );
        }

        return (float)$value;
    }
}
