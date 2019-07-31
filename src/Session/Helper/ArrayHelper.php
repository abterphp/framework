<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Session\Helper;

class ArrayHelper
{
    /**
     * @param array $errors
     *
     * @return array
     */
    public static function flatten(array $errors): array
    {
        $result = [];

        foreach ($errors as $error) {
            if (is_scalar($error)) {
                $result[] = $error;
            } else {
                $result = array_merge($result, self::flatten($error));
            }
        }

        return $result;
    }
}
