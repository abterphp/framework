<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Routing\Bootstrappers\RouterBootstrapper as BaseBootstrapper;
use Opulence\Routing\Router;
use Opulence\Routing\Routes\Caching\ICache;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * Defines the router bootstrapper
 */
class RouterBootstrapper extends BaseBootstrapper
{
    /**
     * Configures the router, which is useful for things like caching
     *
     * @param Router $router The router to configure
     */
    protected function configureRouter(Router $router)
    {
        global $abterModuleManager;

        $httpConfigPath   = Config::get('paths', 'config.http');
        $routingConfig    = require "$httpConfigPath/routing.php";
        $routesConfigPath = "$httpConfigPath/routes.php";

        if (empty($routingConfig['cache']) && Environment::getVar(Env::ENV_NAME) === Environment::PRODUCTION) {
            $cachedRoutesPath = Config::get('paths', 'routes.cache') . '/' . ICache::DEFAULT_CACHED_ROUTES_FILE_NAME;
            $routes           = $this->cache->get($cachedRoutesPath, $router, $routesConfigPath);
            $router->setRouteCollection($routes);

            return;
        }

        require $routesConfigPath;

        foreach ($abterModuleManager->getRoutePaths() as $path) {
            require $path;
        }
    }
}
