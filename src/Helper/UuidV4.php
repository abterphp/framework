<?php

namespace AbterPhp\Framework\Helper;

class UuidV4
{
    /**
     * @return string
     * @throws \Exception
     */
    public static function generate()
    {
        $string = \random_bytes(16);
        $string[6] = \chr(\ord($string[6]) & 0x0f | 0x40);
        $string[8] = \chr(\ord($string[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($string), 4));
    }
}
