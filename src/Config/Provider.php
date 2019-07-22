<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;

class Provider
{
    /**
     * @return string
     */
    public function getProblemBaseUrl(): string
    {
        return Environment::getVar(Env::API_PROBLEM_BASE_URL);
    }

    /**
     * @return string
     */
    public function getAdminDateFormat(): string
    {
        return Environment::getVar(Env::ADMIN_DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getAdminDateTimeFormat(): string
    {
        return Environment::getVar(Env::ADMIN_DATETIME_FORMAT);
    }

    /**
     * @return bool
     */
    public function isCacheAllowed(): bool
    {
        return Environment::getVar(Env::ENV_NAME) === Environment::PRODUCTION;
    }
}
