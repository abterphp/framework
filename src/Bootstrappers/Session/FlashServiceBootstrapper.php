<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Session;

use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Sessions\ISession;

class FlashServiceBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            FlashService::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        $session    = $container->resolve(ISession::class);
        $translator = $container->resolve(ITranslator::class);

        $flashService = new FlashService($session, $translator);

        $container->bindInstance(FlashService::class, $flashService);
    }
}
