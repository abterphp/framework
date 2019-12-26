<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

class Mysql
{
    public const OPTION_EMPTY       = 0;
    public const OPTION_PREFER_DATE = 1;

    /**
     * @param     $value
     * @param int $type
     * @param int $option
     *
     * @return string[]
     */
    public static function nullableParam($value, int $type = \PDO::PARAM_STR, int $option = self::OPTION_EMPTY): array
    {
        if ($value === null) {
            return [null, \PDO::PARAM_NULL];
        }

        if ($value instanceof \DateTime) {
            if ($option & static::OPTION_PREFER_DATE) {
                return [DateHelper::mysqlDate($value), $type];
            }

            return [DateHelper::mysqlDateTime($value), $type];
        }

        return [$value, $type];
    }
}
