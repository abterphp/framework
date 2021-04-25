<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use Exception;
use function bin2hex;
use function chr;
use function ord;
use function random_bytes;
use function str_split;

class UuidV4
{
    /**
     * @see https://stackoverflow.com/a/15875555
     *
     * @return string
     * @throws Exception
     */
    public static function generate(): string
    {
        $string    = random_bytes(16);
        $string[6] = chr(ord($string[6]) & 0x0f | 0x40); // set version to 0100
        $string[8] = chr(ord($string[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        $byteSlices = str_split(bin2hex($string), 4);
        assert(!is_bool($byteSlices));

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', $byteSlices);
    }
}
