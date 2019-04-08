<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Views\Bootstrappers\ViewBootstrapper as BaseBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Caching\ArrayCache;
use Opulence\Views\Caching\FileCache;
use Opulence\Views\Caching\ICache;

/**
 * Defines the view bootstrapper
 */
class ViewBootstrapper extends BaseBootstrapper
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * Gets the view cache
     * To use a different view cache than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     *
     * @return ICache The view cache
     */
    protected function getViewCache(IContainer $container): ICache
    {
        switch (Config::get('views', 'cache')) {
            case ArrayCache::class:
                return new ArrayCache();
            default:
                return new FileCache(
                    Config::get('paths', 'views.compiled'),
                    Config::get('views', 'cache.lifetime'),
                    Config::get('views', 'gc.chance'),
                    Config::get('views', 'gc.divisor')
                );
        }
    }
}
