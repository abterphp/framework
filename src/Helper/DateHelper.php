<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;

class DateHelper
{
    const MYSQL_DATE_FORMAT     = "Y-m-d";
    const MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";

    /**
     * @param \DateTime|null $dateTime
     *
     * @return string
     */
    public static function format(?\DateTime $dateTime): string
    {
        if (!$dateTime) {
            return '';
        }

        return $dateTime->format(Environment::getVar(Env::ADMIN_DATE_FORMAT));
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return string
     */
    public static function formatDateTime(?\DateTime $dateTime): string
    {
        if (!$dateTime) {
            return '';
        }

        return $dateTime->format(Environment::getVar(Env::ADMIN_DATETIME_FORMAT));
    }

    /**
     * @param \DateTime|null $date
     *
     * @return string
     */
    public static function mysqlDate(?\DateTime $date = null): string
    {
        if (!$date) {
            $date = new \DateTime();
        }

        return $date->format(static::MYSQL_DATE_FORMAT);
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return string
     */
    public static function mysqlDateTime(?\DateTime $dateTime = null): string
    {
        if (!$dateTime) {
            $dateTime = new \DateTime();
        }

        return $dateTime->format(static::MYSQL_DATETIME_FORMAT);
    }
}
