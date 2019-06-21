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
}
