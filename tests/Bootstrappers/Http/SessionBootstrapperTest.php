<?php

namespace AbterPhp\Framework\Bootstrappers\Http;

use Opulence\Cache\ArrayBridge;
use Opulence\Cache\FileBridge;
use Opulence\Cache\MemcachedBridge;
use Opulence\Cache\RedisBridge;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Container;
use Opulence\Ioc\IocException;
use Opulence\Memcached\Memcached;
use Opulence\Redis\Redis;
use Opulence\Sessions\Handlers\ArraySessionHandler;
use Opulence\Sessions\Handlers\CacheSessionHandler;
use Opulence\Sessions\Handlers\FileSessionHandler;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\TestCase;
use SessionHandlerInterface;

class SessionBootstrapperTest extends TestCase
{
    /** @var SessionBootstrapper */
    protected SessionBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new SessionBootstrapper();
    }

    protected function tearDown(): void
    {
        Config::set('sessions', 'name', '');
        Config::set('sessions', 'handler', '');
        Config::set('sessions', 'file.path', '');
        Config::set('sessions', 'cache.bridge', '');
        Config::set('sessions', 'cache.clientName', '');
        Config::set('sessions', 'cache.keyPrefix', '');
    }

    public function testRegisterBindingsDefault()
    {
        Config::set('sessions', 'name', 'foo');
        Config::set('sessions', 'file.path', 'baz');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ISession::class);
        $this->assertInstanceOf(ISession::class, $actual);

        $actual = $container->resolve(SessionHandlerInterface::class);
        $this->assertInstanceOf(FileSessionHandler::class, $actual);
    }

    /**
     * @return array
     */
    public function cacheBridgeDataProvider(): array
    {
        return [
            [ArrayBridge::class],
            [MemcachedBridge::class],
            [RedisBridge::class],
            [FileBridge::class],
        ];
    }

    /**
     * @dataProvider cacheBridgeDataProvider
     *
     * @param string $cacheBridge
     *
     * @throws IocException
     */
    public function testRegisterBindingsCacheSession(string $cacheBridge)
    {
        Config::set('sessions', 'name', 'foo');
        Config::set('sessions', 'file.path', 'baz');
        Config::set('sessions', 'handler', CacheSessionHandler::class);
        Config::set('sessions', 'lifetime', 123);
        Config::set('sessions', 'cache.bridge', $cacheBridge);
        Config::set('sessions', 'cache.clientName', 'qux');
        Config::set('sessions', 'cache.keyPrefix', 'quix');

        $memcachedStub = $this->getMockBuilder(Memcached::class)->disableOriginalConstructor()->getMock();
        $redisStub = $this->getMockBuilder(Redis::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(Memcached::class, $memcachedStub);
        $container->bindInstance(Redis::class, $redisStub);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ISession::class);
        $this->assertInstanceOf(ISession::class, $actual);

        $actual = $container->resolve(SessionHandlerInterface::class);
        $this->assertInstanceOf(CacheSessionHandler::class, $actual);
    }

    public function testRegisterBindingsEncryptedCacheSession()
    {
        Config::set('sessions', 'name', 'foo');
        Config::set('sessions', 'file.path', 'baz');
        Config::set('sessions', 'handler', CacheSessionHandler::class);
        Config::set('sessions', 'lifetime', 123);
        Config::set('sessions', 'isEncrypted', 1);

        $encrypterMock = $this->getMockBuilder(IEncrypter::class)->getMock();

        $container = new Container();
        $container->bindInstance(IEncrypter::class, $encrypterMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ISession::class);
        $this->assertInstanceOf(ISession::class, $actual);

        $actual = $container->resolve(SessionHandlerInterface::class);
        $this->assertInstanceOf(CacheSessionHandler::class, $actual);
    }

    public function testRegisterBindingsArraySessionHandler()
    {
        Config::set('sessions', 'name', 'foo');
        Config::set('sessions', 'handler', ArraySessionHandler::class);

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ISession::class);
        $this->assertInstanceOf(ISession::class, $actual);

        $actual = $container->resolve(SessionHandlerInterface::class);
        $this->assertInstanceOf(ArraySessionHandler::class, $actual);
    }
}
