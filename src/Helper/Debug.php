<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

class Debug
{
    /**
     * @param string $str
     * @param mixed  $val
     * @param mixed  ...$extra
     *
     * @return string
     */
    public static function prettyPrint(string $str, $val, ...$extra): string
    {
        return sprintf($str, print_r($val, true), ...$extra);
    }
}
