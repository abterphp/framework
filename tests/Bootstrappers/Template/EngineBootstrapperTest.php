<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Template;

use AbterPhp\Framework\Template\CacheManager;
use AbterPhp\Framework\Template\Engine;
use AbterPhp\Framework\Template\Renderer;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class EngineBootstrapperTest extends TestCase
{
    /** @var EngineBootstrapper - System Under Test */
    protected EngineBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new EngineBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $eventDispatcherMock = $this->getMockBuilder(IEventDispatcher::class)->getMock();
        $cacheManagerMock    = $this->getMockBuilder(CacheManager::class)->disableOriginalConstructor()->getMock();
        $rendererMock        = $this->getMockBuilder(Renderer::class)->disableOriginalConstructor()->getMock();

        $container = new Container();
        $container->bindInstance(IEventDispatcher::class, $eventDispatcherMock);
        $container->bindInstance(CacheManager::class, $cacheManagerMock);
        $container->bindInstance(Renderer::class, $rendererMock);

        $eventDispatcherMock->expects($this->once())->method('dispatch');

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Engine::class);
        $this->assertInstanceOf(Engine::class, $actual);
    }
}
