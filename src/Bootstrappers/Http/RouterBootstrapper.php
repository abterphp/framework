<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Routing\Bootstrappers\RouterBootstrapper as BaseBootstrapper;
use Opulence\Routing\Router;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * Defines the router bootstrapper
 */
class RouterBootstrapper extends BaseBootstrapper
{
    protected ?array $routePaths = null;

    /**
     * @return array
     */
    public function getRoutePaths(): array
    {
        global $abterModuleManager;

        if ($this->routePaths !== null) {
            return $this->routePaths;
        }

        $paths = $abterModuleManager->getRoutePaths() ?: [];

        $this->routePaths = $paths;

        return $paths;
    }

    /**
     * @param array $routePaths
     *
     * @return $this
     */
    public function setRoutePaths(array $routePaths): self
    {
        $this->routePaths = $routePaths;

        return $this;
    }


    /**
     * Configures the router, which is useful for things like caching
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Router $router The router to configure
     */
    protected function configureRouter(Router $router): void
    {
        $httpConfigPath   = Config::get('paths', 'config.http');
        $routesConfigPath = "$httpConfigPath/routes.php";

        require $routesConfigPath;

        foreach ($this->getRoutePaths() as $path) {
            require $path;
        }
    }
}
