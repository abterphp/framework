<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Navigation;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Navigation as NavConstant;
use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Navigation\Navigation;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class NavigationBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /** @var array */
    protected $bindingIntents = [
        NavConstant::NAVBAR  => [Navigation::INTENT_NAVBAR],
        NavConstant::PRIMARY => [Navigation::INTENT_PRIMARY],
    ];

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return array_keys($this->bindingIntents);
    }

    /**
     * @param IContainer $container
     *
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        /** @var ITranslator $translator */
        $translator = $container->resolve(ITranslator::class);

        /** @var IEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->resolve(IEventDispatcher::class);

        foreach ($this->bindingIntents as $name => $intents) {
            $navigation = new Navigation($intents);

            $container->bindInstance($name, $navigation);

            $eventDispatcher->dispatch(Event::NAVIGATION_READY, new NavigationReady($navigation));

            $navigation->setTranslator($translator);
        }
    }
}
