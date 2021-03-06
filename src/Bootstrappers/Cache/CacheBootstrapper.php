<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Cache;

use AbterPhp\Framework\Config\Config;
use Opulence\Cache\ArrayBridge;
use Opulence\Cache\FileBridge;
use Opulence\Cache\ICacheBridge;
use Opulence\Cache\MemcachedBridge;
use Opulence\Cache\RedisBridge;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Memcached\Memcached;
use Opulence\Redis\Redis;

class CacheBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            ICacheBridge::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        $cacheBridge = $this->getCacheBridge($container);

        $container->bindInstance(ICacheBridge::class, $cacheBridge);
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
        switch (Config::get('cache', 'cache.bridge')) {
            case ArrayBridge::class:
                return new ArrayBridge();
            case MemcachedBridge::class:
                /** @var Memcached $memcached */
                $memcached = $container->resolve(Memcached::class);

                $clientName = Config::mustGetString('cache', 'cache.clientName');
                $keyPrefix = Config::mustGetString('cache', 'cache.keyPrefix');

                return new MemcachedBridge($memcached, $clientName, $keyPrefix);
            case RedisBridge::class:
                /** @var Redis $redis */
                $redis = $container->resolve(Redis::class);

                $clientName = Config::mustGetString('cache', 'cache.clientName');
                $keyPrefix = Config::mustGetString('cache', 'cache.keyPrefix');

                return new RedisBridge($redis, $clientName, $keyPrefix);
            default:
                $filePath = Config::mustGetString('authorization', 'file.path');

                return new FileBridge($filePath);
        }
    }
}
