<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Env
{
    const ENV_NAME = 'ENV_NAME';

    const DB_HOST     = 'DB_HOST';
    const DB_USER     = 'DB_USER';
    const DB_PASSWORD = 'DB_PASSWORD';
    const DB_NAME     = 'DB_NAME';
    const DB_PORT     = 'DB_PORT';
    const DB_DRIVER   = 'DB_DRIVER';

    const MEMCACHED_HOST = 'MEMCACHED_HOST';
    const MEMCACHED_PORT = 'MEMCACHED_PORT';

    const REDIS_HOST     = 'REDIS_HOST';
    const REDIS_PORT     = 'REDIS_PORT';
    const REDIS_DATABASE = 'REDIS_DATABASE';

    const ENCRYPTION_KEY = 'ENCRYPTION_KEY';

    const DEFAULT_LANGUAGE = 'DEFAULT_LANGUAGE';

    const DIR_PRIVATE     = 'DIR_PRIVATE';
    const DIR_PUBLIC      = 'DIR_PUBLIC';
    const DIR_AUTH_CONFIG = 'DIR_AUTH_CONFIG';
    const DIR_MIGRATIONS  = 'DIR_MIGRATIONS';
    const DIR_LOGS        = 'DIR_LOGS';
    const DIR_ROOT_JS     = 'DIR_ROOT_JS';
    const DIR_ROOT_CSS    = 'DIR_ROOT_CSS';
    const DIR_CACHE_JS    = 'DIR_CACHE_JS';
    const DIR_CACHE_CSS   = 'DIR_CACHE_CSS';

    const PATH_CACHE_JS  = 'PATH_CACHE_JS';
    const PATH_CACHE_CSS = 'PATH_CACHE_CSS';

    const CRYPTO_FRONTEND_SALT     = 'CRYPTO_FRONTEND_SALT';
    const CRYPTO_ENCRYPTION_PEPPER = 'CRYPTO_ENCRYPTION_PEPPER';
    const CRYPTO_BCRYPT_SALT       = 'CRYPTO_BCRYPT_SALT';
    const CRYPTO_BCRYPT_COST       = 'CRYPTO_BCRYPT_COST';

    const PAGINATION_SIZE_OPTIONS = 'PAGINATION_SIZE_OPTIONS';
    const PAGINATION_SIZE_DEFAULT = 'PAGINATION_SIZE_DEFAULT';
    const PAGINATION_NUMBER_COUNT = 'PAGINATION_NUMBER_COUNT';

    const EMAIL_SMTP_HOST        = 'EMAIL_SMTP_HOST';
    const EMAIL_SMTP_PORT        = 'EMAIL_SMTP_PORT';
    const EMAIL_SMTP_ENCRYPTION  = 'EMAIL_SMTP_ENCRYPTION';
    const EMAIL_SMTP_USERNAME    = 'EMAIL_SMTP_USERNAME';
    const EMAIL_SMTP_PASSWORD    = 'EMAIL_SMTP_PASSWORD';
    const EMAIL_SENDMAIL_COMMAND = 'EMAIL_SENDMAIL_COMMAND';

    const ADMIN_DATE_FORMAT     = 'ADMIN_DATE_FORMAT';
    const ADMIN_DATETIME_FORMAT = 'ADMIN_DATETIME_FORMAT';

    const MODULE_CACHE_KEY = 'MODULE_CACHE_KEY';

    const GENERAL_CACHE_BRIDGE  = 'GENERAL_CACHE_BRIDGE';
    const AUTH_CACHE_BRIDGE     = 'AUTH_CACHE_BRIDGE';
    const TEMPLATE_CACHE_BRIDGE = 'TEMPLATE_CACHE_BRIDGE';

    const SESSION_HANDLER          = 'SESSION_HANDLER';
    const SESSION_CACHE_BRIDGE     = 'SESSION_CACHE_BRIDGE';
    const SESSION_COOKIE_DOMAIN    = 'SESSION_COOKIE_DOMAIN';
    const SESSION_COOKIE_IS_SECURE = 'SESSION_COOKIE_IS_SECURE';
    const SESSION_COOKIE_PATH      = 'SESSION_COOKIE_PATH';

    const OAUTH2_PRIVATE_KEY_PATH     = 'OAUTH2_PRIVATE_KEY_PATH';
    const OAUTH2_PRIVATE_KEY_PASSWORD = 'OAUTH2_PRIVATE_KEY_PASSWORD';
    const OAUTH2_PUBLIC_KEY_PATH      = 'OAUTH2_PUBLIC_KEY_PATH';
    const OAUTH2_ENCRYPTION_KEY       = 'OAUTH2_ENCRYPTION_KEY';
    const OAUTH2_TOKEN_EXPIRY         = 'OAUTH2_TOKEN_EXPIRY';
    const OAUTH2_SECRET_LENGTH        = 'OAUTH2_SECRET_LENGTH';

    const LOGIN_MAX_ATTEMPTS = 'LOGIN_MAX_ATTEMPTS';
    const LOGIN_LOG_IP       = 'LOGIN_LOG_IP';

    const VIEW_CACHE = 'VIEW_CACHE';

    const API_PROBLEM_BASE_URL = 'API_PROBLEM_BASE_URL';
}
