<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Routes
{
    const ROUTE_ASSET = 'asset';
    const ROUTE_404   = '404';

    const PATH_ASSET = '/:path';
    const PATH_404   = '/:anything';

    const VAR_ANYTHING = 'anything';
    const VAR_PATH     = 'path';
}
