<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Route
{
    public const OPTION_NAME       = 'name';
    public const OPTION_VARS       = 'vars';
    public const OPTION_MIDDLEWARE = 'middleware';

    public const INDEX = 'index';

    public const FALLBACK = 'fallback';

    public const ASSET = 'asset';

    public const NOT_ALLOWED = 'not-allowed'; // 403

    public const NOT_FOUND = 'not-found'; // 404

    public const VAR_ANYTHING = 'anything';
    public const VAR_PATH     = 'path';
}
