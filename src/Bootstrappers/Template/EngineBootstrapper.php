<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Template;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Events\TemplateEngineReady;
use AbterPhp\Framework\Template\CacheManager;
use AbterPhp\Framework\Template\Engine;
use AbterPhp\Framework\Template\Renderer;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

class EngineBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
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
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        /** @var IEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->resolve(IEventDispatcher::class);

        /** @var CacheManager $cacheManager */
        $cacheManager = $container->resolve(CacheManager::class);

        /** @var Renderer $renderer */
        $renderer = $container->resolve(Renderer::class);

        $isCacheAllowed = (Environment::mustGetVar(Env::ENV_NAME) !== Environment::DEVELOPMENT);

        $templateEngine = new Engine($renderer, $cacheManager, $isCacheAllowed);
        $eventDispatcher->dispatch(Event::TEMPLATE_ENGINE_READY, new TemplateEngineReady($templateEngine));

        $container->bindInstance(Engine::class, $templateEngine);
    }
}
