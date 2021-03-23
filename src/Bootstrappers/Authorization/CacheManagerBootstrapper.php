<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Authorization;

use AbterPhp\Framework\Authorization\CacheManager;
use Opulence\Cache\ArrayBridge;
use Opulence\Cache\FileBridge;
use Opulence\Cache\ICacheBridge;
use Opulence\Cache\MemcachedBridge;
use Opulence\Cache\RedisBridge;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Memcached\Memcached;
use Opulence\Redis\Redis;

class CacheManagerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            CacheManager::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        $cacheBridge = $this->getCacheBridge($container);

        $cacheManager = new CacheManager($cacheBridge);

        $container->bindInstance(CacheManager::class, $cacheManager);
    }

    /**
     * Gets the cache bridge to use for a cache session handler
     *
     * @param IContainer $container The IoC container
     *
     * @return ICacheBridge The cache bridge
     * @throws IocException Thrown if the cache bridge could not be resolved
     */
    private function getCacheBridge(IContainer $container): ICacheBridge
    {
        switch (Config::get('authorization', 'cache.bridge')) {
            case ArrayBridge::class:
                return new ArrayBridge();
            case MemcachedBridge::class:
                return new MemcachedBridge(
                    $container->resolve(Memcached::class),
                    Config::get('authorization', 'cache.clientName'),
                    Config::get('authorization', 'cache.keyPrefix')
                );
            case RedisBridge::class:
                return new RedisBridge(
                    $container->resolve(Redis::class),
                    Config::get('authorization', 'cache.clientName'),
                    Config::get('authorization', 'cache.keyPrefix')
                );
            default:
                // FileBridge
                return new FileBridge(Config::get('authorization', 'file.path'));
        }
    }
}
