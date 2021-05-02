<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use AbterPhp\Framework\Config\Config;
use Opulence\Cache\ArrayBridge;
use Opulence\Cache\FileBridge;
use Opulence\Cache\ICacheBridge;
use Opulence\Cache\MemcachedBridge;
use Opulence\Cache\RedisBridge;
use Opulence\Framework\Sessions\Bootstrappers\SessionBootstrapper as BaseBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Memcached\Memcached;
use Opulence\Redis\Redis;
use Opulence\Sessions\Handlers\ArraySessionHandler;
use Opulence\Sessions\Handlers\CacheSessionHandler;
use Opulence\Sessions\Handlers\FileSessionHandler;
use Opulence\Sessions\ISession;
use Opulence\Sessions\Session;
use SessionHandlerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * Defines the session bootstrapper
 */
class SessionBootstrapper extends BaseBootstrapper
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * Gets the session object to use
     *
     * @param IContainer $container The IoC Container
     *
     * @return ISession The session to use
     */
    protected function getSession(IContainer $container): ISession
    {
        $session = new Session();
        $session->setName(Config::mustGetString('sessions', 'name'));

        return $session;
    }

    /**
     * Gets the session handler object to use
     *
     * @param IContainer $container The IoC Container
     *
     * @return SessionHandlerInterface The session handler to use
     * @throws IocException Thrown if the encrypter could not be resolved
     */
    protected function getSessionHandler(IContainer $container): SessionHandlerInterface
    {
        switch (Config::mustGetString('sessions', 'handler')) {
            case CacheSessionHandler::class:
                $handler = new CacheSessionHandler(
                    $this->getCacheBridge($container),
                    Config::mustGetInt('sessions', 'lifetime')
                );
                if (Config::mustGetBool('sessions', 'isEncrypted')) {
                    $handler->useEncryption(true);
                    $handler->setEncrypter($this->getSessionEncrypter($container));
                }
                break;
            case ArraySessionHandler::class:
                $handler = new ArraySessionHandler();
                break;
            default: // FileSessionHandler
                $handler = new FileSessionHandler(Config::mustGetString('sessions', 'file.path'));
        }

        return $handler;
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
        switch (Config::mustGetString('sessions', 'cache.bridge')) {
            case ArrayBridge::class:
                return new ArrayBridge();
            case MemcachedBridge::class:
                return new MemcachedBridge(
                    $container->resolve(Memcached::class),
                    Config::mustGetString('sessions', 'cache.clientName'),
                    Config::mustGetString('sessions', 'cache.keyPrefix')
                );
            case RedisBridge::class:
                return new RedisBridge(
                    $container->resolve(Redis::class),
                    Config::mustGetString('sessions', 'cache.clientName'),
                    Config::mustGetString('sessions', 'cache.keyPrefix')
                );
            default: // FileBridge
                return new FileBridge(Config::mustGetString('sessions', 'file.path'));
        }
    }
}
