<?php

declare(strict_types=1);

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

        /** @see \AbterPhp\Framework\Http\Controllers\Website\Index::notFound() */
        $router->any(
            '/:anything',
            'Website\Index@notFound',
            [
                Route::OPTION_NAME => Route::NOT_FOUND,
                Route::OPTION_VARS => [Route::VAR_ANYTHING => '.+'],
            ]
        );
    }
);
