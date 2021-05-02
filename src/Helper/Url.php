<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

class Url
{
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
