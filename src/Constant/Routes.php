<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Routes
{
    public const ROUTE_ASSET = 'asset';
    public const ROUTE_404   = '404';

    public const PATH_ASSET = '/:path';
    public const PATH_404   = '/:anything';

    public const VAR_ANYTHING = 'anything';
    public const VAR_PATH     = 'path';
}
