<?php

declare(strict_types=1);

use AbterPhp\Framework\Config\Routes;
use AbterPhp\Framework\Constant\Route;
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
            Routes::getAssetsPath(),
            'Website\Assets@asset',
            [
                Route::OPTION_NAME => Route::ASSET_CACHE,
                Route::OPTION_VARS => [Route::VAR_PATH => '(.+)\.([\w\d\?]+)'],
            ]
        );

        /** @see \AbterPhp\Framework\Http\Controllers\Website\Assets::asset() */
        $router->get(
            '/:path',
            'Website\Assets@asset',
            [
                Route::OPTION_NAME => Route::ASSET,
                Route::OPTION_VARS => [Route::VAR_PATH => '(.+)\.([\w\d\?]+)'],
            ]
        );
    }
);
