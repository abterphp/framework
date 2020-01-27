<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Env
{
    public const ENV_NAME = 'ENV_NAME';

    public const DB_HOST     = 'DB_HOST';
    public const DB_USER     = 'DB_USER';
    public const DB_PASSWORD = 'DB_PASSWORD';
    public const DB_NAME     = 'DB_NAME';
    public const DB_PORT     = 'DB_PORT';
    public const DB_DRIVER   = 'DB_DRIVER';

    public const MEMCACHED_HOST = 'MEMCACHED_HOST';
    public const MEMCACHED_PORT = 'MEMCACHED_PORT';

    public const REDIS_HOST     = 'REDIS_HOST';
    public const REDIS_PORT     = 'REDIS_PORT';
    public const REDIS_DATABASE = 'REDIS_DATABASE';

    public const ENCRYPTION_KEY = 'ENCRYPTION_KEY';

    public const DEFAULT_LANGUAGE = 'DEFAULT_LANGUAGE';

    public const DIR_PRIVATE     = 'DIR_PRIVATE';
    public const DIR_PUBLIC      = 'DIR_PUBLIC';
    public const DIR_MEDIA       = 'DIR_MEDIA';
    public const DIR_AUTH_CONFIG = 'DIR_AUTH_CONFIG';
    public const DIR_MIGRATIONS  = 'DIR_MIGRATIONS';
    public const DIR_LOGS        = 'DIR_LOGS';

    public const MEDIA_BASE_URL  = 'MEDIA_BASE_URL';
    public const CACHE_BASE_PATH = 'CACHE_BASE_PATH';

    public const CRYPTO_FRONTEND_SALT     = 'CRYPTO_FRONTEND_SALT';
    public const CRYPTO_ENCRYPTION_PEPPER = 'CRYPTO_ENCRYPTION_PEPPER';
    public const CRYPTO_BCRYPT_SALT       = 'CRYPTO_BCRYPT_SALT';
    public const CRYPTO_BCRYPT_COST       = 'CRYPTO_BCRYPT_COST';

    public const PAGINATION_SIZE_OPTIONS = 'PAGINATION_SIZE_OPTIONS';
    public const PAGINATION_SIZE_DEFAULT = 'PAGINATION_SIZE_DEFAULT';
    public const PAGINATION_NUMBER_COUNT = 'PAGINATION_NUMBER_COUNT';

    public const EMAIL_SMTP_HOST        = 'EMAIL_SMTP_HOST';
    public const EMAIL_SMTP_PORT        = 'EMAIL_SMTP_PORT';
    public const EMAIL_SMTP_ENCRYPTION  = 'EMAIL_SMTP_ENCRYPTION';
    public const EMAIL_SMTP_USERNAME    = 'EMAIL_SMTP_USERNAME';
    public const EMAIL_SMTP_PASSWORD    = 'EMAIL_SMTP_PASSWORD';
    public const EMAIL_SENDMAIL_COMMAND = 'EMAIL_SENDMAIL_COMMAND';

    public const MODULE_CACHE_KEY = 'MODULE_CACHE_KEY';

    public const GENERAL_CACHE_BRIDGE  = 'GENERAL_CACHE_BRIDGE';
    public const AUTH_CACHE_BRIDGE     = 'AUTH_CACHE_BRIDGE';
    public const TEMPLATE_CACHE_BRIDGE = 'TEMPLATE_CACHE_BRIDGE';

    public const SESSION_HANDLER          = 'SESSION_HANDLER';
    public const SESSION_CACHE_BRIDGE     = 'SESSION_CACHE_BRIDGE';
    public const SESSION_COOKIE_DOMAIN    = 'SESSION_COOKIE_DOMAIN';
    public const SESSION_COOKIE_IS_SECURE = 'SESSION_COOKIE_IS_SECURE';
    public const SESSION_COOKIE_PATH      = 'SESSION_COOKIE_PATH';

    public const OAUTH2_PRIVATE_KEY_PATH     = 'OAUTH2_PRIVATE_KEY_PATH';
    public const OAUTH2_PRIVATE_KEY_PASSWORD = 'OAUTH2_PRIVATE_KEY_PASSWORD';
    public const OAUTH2_PUBLIC_KEY_PATH      = 'OAUTH2_PUBLIC_KEY_PATH';
    public const OAUTH2_ENCRYPTION_KEY       = 'OAUTH2_ENCRYPTION_KEY';
    public const OAUTH2_TOKEN_EXPIRY         = 'OAUTH2_TOKEN_EXPIRY';
    public const OAUTH2_SECRET_LENGTH        = 'OAUTH2_SECRET_LENGTH';

    public const LOGIN_MAX_ATTEMPTS = 'LOGIN_MAX_ATTEMPTS';
    public const LOGIN_LOG_IP       = 'LOGIN_LOG_IP';

    public const VIEW_CACHE = 'VIEW_CACHE';

    public const API_PROBLEM_BASE_URL = 'API_PROBLEM_BASE_URL';
}
