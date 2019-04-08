<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Session;

use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Sessions\ISession;

class FlashServiceBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            FlashService::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $session    = $container->resolve(ISession::class);
        $translator = $container->resolve(ITranslator::class);

        $flashService = new FlashService($session, $translator);

        $container->bindInstance(FlashService::class, $flashService);
    }
}
