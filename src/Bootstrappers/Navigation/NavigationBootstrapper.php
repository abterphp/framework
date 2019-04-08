<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Navigation;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Navigation as NavConstant;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Navigation\Navigation;
use Casbin\Enforcer;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Sessions\ISession;

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

        /** @var ISession $session */
        $session  = $container->resolve(ISession::class);
        $username = (string)$session->get(Session::USERNAME, '');

        /** @var Enforcer $enforcer */
        $enforcer = $container->hasBinding(Enforcer::class) ? $container->resolve(Enforcer::class) : null;

        foreach ($this->bindingIntents as $name => $intents) {
            $navigation = $this->createNavigation($enforcer, $username, ...$intents);

            $container->bindInstance($name, $navigation);

            $eventDispatcher->dispatch(Event::NAVIGATION_READY, new NavigationReady($navigation));

            $navigation->setTranslator($translator);
        }
    }

    /**
     * @param Enforcer|null $enforcer
     * @param string        $username
     * @param string        ...$intents
     *
     * @return Navigation
     */
    protected function createNavigation(?Enforcer $enforcer, string $username, string ...$intents): Navigation
    {
        $navigation = new Navigation(
            $username,
            $intents,
            [],
            $enforcer
        );

        return $navigation;
    }
}
