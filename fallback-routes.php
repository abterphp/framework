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

        /** @see \AbterPhp\Framework\Http\Controllers\Website\Index::notFound() */
        $router->any(
            Routes::PATH_404,
            'Website\Index@notFound',
            [
                OPTION_NAME => Routes::ROUTE_404,
                OPTION_VARS => [Routes::VAR_ANYTHING => '.+'],
            ]
        );
    }
);
