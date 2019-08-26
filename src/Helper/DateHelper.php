<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;

class DateHelper
{
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
}
