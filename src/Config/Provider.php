<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;

class Provider
{
    /**
     * @return string
     */
    public function getProblemBaseUrl(): string
    {
        return getenv(Env::API_PROBLEM_BASE_URL);
    }

    /**
     * @return string
     */
    public function getAdminDateFormat(): string
    {
        return getenv(Env::ADMIN_DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getAdminDateTimeFormat(): string
    {
        return getenv(Env::ADMIN_DATETIME_FORMAT);
    }
}
