<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Cache;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Opulence\Cache\ArrayBridge;
use Opulence\Cache\FileBridge;
use Opulence\Cache\ICacheBridge;
use Opulence\Cache\MemcachedBridge;
use Opulence\Cache\RedisBridge;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Container;
use Opulence\Memcached\Memcached;
use Opulence\Redis\Redis;
use PHPUnit\Framework\TestCase;

class CacheBootstrapperTest extends TestCase
{
    /** @var CacheBootstrapper - System Under Test */
    protected CacheBootstrapper $sut;

    public function setUp(): void
    {
        Environment::unsetVar(Env::ENV_NAME);

        $this->sut = new CacheBootstrapper();
    }

    protected function tearDown(): void
    {
        Config::set('cache', 'file.path', '');
        Config::set('cache', 'cache.bridge', '');
        Config::set('cache', 'cache.clientName', '');
        Config::set('cache', 'cache.keyPrefix', '');
    }

    public function testRegisterBindingsFileBridge(): void
    {
        Config::set('cache', 'file.path', '/tmp');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ICacheBridge::class);
        $this->assertInstanceOf(FileBridge::class, $actual);
    }

    public function testRegisterBindingsArrayBridge(): void
    {
        Config::set('cache', 'cache.bridge', ArrayBridge::class);

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ICacheBridge::class);
        $this->assertInstanceOf(ArrayBridge::class, $actual);
    }

    public function testRegisterBindingsMemcachedBridge(): void
    {
        Config::set('cache', 'cache.bridge', MemcachedBridge::class);
        Config::set('cache', 'cache.clientName', 'foo');
        Config::set('cache', 'cache.keyPrefix', 'bar');

        $mockMemcached = $this->getMockBuilder(Memcached::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(Memcached::class, $mockMemcached);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ICacheBridge::class);
        $this->assertInstanceOf(MemcachedBridge::class, $actual);
    }

    public function testRegisterBindingsRedisBridge(): void
    {
        Config::set('cache', 'cache.bridge', RedisBridge::class);
        Config::set('cache', 'cache.clientName', 'foo');
        Config::set('cache', 'cache.keyPrefix', 'bar');

        $mockRedis = $this->getMockBuilder(Redis::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(Redis::class, $mockRedis);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ICacheBridge::class);
        $this->assertInstanceOf(RedisBridge::class, $actual);
    }
}
