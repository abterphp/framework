<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

class ArrayHelper
{
    protected const ERROR_INVALID_ATTRIBUTES = 'invalid attributes (array of string[] and null items)';

    /**
     * @param array<string,string> $styles
     *
     * @return string
     */
    public static function toStyles(array $styles): string
    {
        $tmp = [];
        foreach ($styles as $k => $v) {
            $tmp[] = sprintf('%s: %s', $k, $v);
        }

        return implode('; ', $tmp);
    }

    /**
     * @param array<string,string[]> $attributes
     * @param string                 $prefix string to prepend if the result is not empty
     *
     * @return string
     */
    public static function toAttributes(array $attributes, string $prefix = ' '): string
    {
        $tmp = [];
        foreach ($attributes as $k => $v) {
            if (null === $v) {
                $tmp[] = (string)$k;
                continue;
            }

            $v = is_array($v) ? implode(' ', $v) : $v;

            $tmp[] = sprintf('%s="%s"', $k, $v);
        }

        if (empty($tmp)) {
            return '';
        }

        return $prefix . implode(' ', $tmp);
    }

    /**
     * Merges two sets of attributes, but without filtering out duplicates of any sort
     * (Developed to be used in factories before Component creation)
     *
     * @param array<string,null|string[]> $existing
     * @param array<string,null|string[]> $new
     *
     * @return array<string,null|string[]>
     */
    public static function unsafeMergeAttributes(array $existing, array $new): array
    {
        foreach ($new as $key => $value) {
            if (!isset($existing[$key])) {
                $existing[$key] = $value;
            } else {
                $existing[$key] = array_merge((array)$existing[$key], (array)$new[$key]);
            }
        }

        return $existing;
    }

    /**
     * @param array<string,null|string[]> $existing
     * @param array<string,null|string[]> $new
     * @param bool                        $safe if true, $existing will be formatted, which is of course slower.
     *                                          should only be false if $existing is already formatted
     *
     * @return array
     */
    public static function mergeAttributes(array $existing, array $new, bool $safe = true): array
    {
        if ($safe) {
            foreach ($existing as $key => $value) {
                $existing[$key] = static::formatAttribute($value);
            }
        }

        foreach ($new as $key => $value) {
            $existingValue = isset($existing[$key]) ? $existing[$key] : [];
            $newValue      = static::formatAttribute($value);

            if ($newValue !== null) {
                $existing[$key] = array_merge($existingValue, $newValue);
            }
        }

        return $existing;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * Formats an attribute so that it's either null or an array of strings where:
     *  - keys and values are the same
     *  - keys (or values) must not have spaces
     *
     * @param $value
     *
     * @return array|null
     */
    public static function formatAttribute($value): ?array
    {
        static $realEmpty = [null, '', false];

        if (in_array($value, $realEmpty, true)) {
            return null;
        }

        if (!is_scalar($value) && !is_array($value)) {
            throw new \InvalidArgumentException(static::ERROR_INVALID_ATTRIBUTES);
        }

        if (is_scalar($value)) {
            $value = [(string)$value];
        }

        if (count($value) === 1 && in_array(current($value), $realEmpty, true)) {
            return null;
        }

        $newValue = [];
        foreach ($value as $v) {
            if (!is_scalar($v)) {
                throw new \InvalidArgumentException(static::ERROR_INVALID_ATTRIBUTES);
            }

            foreach (explode(' ', (string)$v) as $w) {
                if ('' === $w) {
                    continue;
                }
                $newValue[$w] = $w;
            }
        }

        return $newValue;
    }

    /**
     * @param array<string,string> $parts
     *
     * @return string
     */
    public static function toQuery(array $parts): string
    {
        if (empty($parts)) {
            return '';
        }

        $tmp = [];
        foreach ($parts as $k => $v) {
            $tmp[] = sprintf('%s=%s', $k, urlencode($v));
        }

        return '?' . implode('&', $tmp);
    }
}
