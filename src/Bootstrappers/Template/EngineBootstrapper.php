<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Template;

use AbterPhp\Framework\Config\Provider as ConfigProvider;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Events\TemplateEngineReady;
use AbterPhp\Framework\Template\CacheManager;
use AbterPhp\Framework\Template\Engine;
use AbterPhp\Framework\Template\Renderer;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class EngineBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            Engine::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        /** @var IEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->resolve(IEventDispatcher::class);

        /** @var CacheManager $cacheManager */
        $cacheManager = $container->resolve(CacheManager::class);

        /** @var Renderer $renderer */
        $renderer = $container->resolve(Renderer::class);

        /** @var ConfigProvider $configProvider */
        $configProvider = $container->resolve(ConfigProvider::class);

        $templateEngine = new Engine($renderer, $cacheManager, $configProvider);
        $eventDispatcher->dispatch(Event::TEMPLATE_ENGINE_READY, new TemplateEngineReady($templateEngine));

        $container->bindInstance(Engine::class, $templateEngine);
    }
}
