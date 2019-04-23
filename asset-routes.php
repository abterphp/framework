<?php

declare(strict_types=1);

use AbterPhp\Framework\Constant\Routes;
use Opulence\Routing\Router;

/**
 * ----------------------------------------------------------
 * Create all of the routes for the HTTP kernel
 * ----------------------------------------------------------
 *
 * @var Router $router
 */
$router->group(
    ['controllerNamespace' => 'AbterPhp\Framework\Http\Controllers'],
    function (Router $router) {

        /** @see \AbterPhp\Framework\Http\Controllers\Website\Assets::asset() */
        $router->get(
            Routes::PATH_ASSET,
            'Website\Assets@asset',
            [
                OPTION_NAME => Routes::ROUTE_ASSET,
                OPTION_VARS => [Routes::VAR_PATH => '(.+)\.(css|js|jpg|jpeg|gif|png|svg|ico|webp)'],
            ]
        );
    }
);
