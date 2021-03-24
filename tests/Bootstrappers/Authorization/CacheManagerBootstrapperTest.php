<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Authorization;

use AbterPhp\Framework\Authorization\CacheManager;
use Opulence\Cache\ArrayBridge;
use Opulence\Cache\MemcachedBridge;
use Opulence\Cache\RedisBridge;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Container;
use Opulence\Memcached\Memcached;
use Opulence\Redis\Redis;
use PHPUnit\Framework\TestCase;

class CacheManagerBootstrapperTest extends TestCase
{
    /** @var CacheManagerBootstrapper - System Under Test */
    protected CacheManagerBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new CacheManagerBootstrapper();
    }

    public function testRegisterBindingsFileBridge(): void
    {
        Config::set('authorization', 'file.path', '/tmp');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(CacheManager::class);
        $this->assertInstanceOf(CacheManager::class, $actual);
    }

    public function testRegisterBindingsArrayBridge(): void
    {
        Config::set('authorization', 'cache.bridge', ArrayBridge::class);

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(CacheManager::class);
        $this->assertInstanceOf(CacheManager::class, $actual);
    }

    public function testRegisterBindingsMemcachedBridge(): void
    {
        Config::set('authorization', 'cache.bridge', MemcachedBridge::class);
        Config::set('authorization', 'cache.clientName', 'foo');
        Config::set('authorization', 'cache.keyPrefix', 'bar');

        $mockMemcached = $this->getMockBuilder(Memcached::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(Memcached::class, $mockMemcached);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(CacheManager::class);
        $this->assertInstanceOf(CacheManager::class, $actual);
    }

    public function testRegisterBindingsRedisBridge(): void
    {
        Config::set('authorization', 'cache.bridge', RedisBridge::class);
        Config::set('authorization', 'cache.clientName', 'foo');
        Config::set('authorization', 'cache.keyPrefix', 'bar');

        $mockRedis = $this->getMockBuilder(Redis::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(Redis::class, $mockRedis);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(CacheManager::class);
        $this->assertInstanceOf(CacheManager::class, $actual);
    }
}
