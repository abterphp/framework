<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

class Css
{
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
}
