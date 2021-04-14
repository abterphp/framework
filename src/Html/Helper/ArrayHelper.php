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
