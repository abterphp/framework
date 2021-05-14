<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Console\Commands\Security;

use AbterPhp\Framework\Console\Commands\Security\SecretGenerator;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Events\SecretGeneratorReady;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

class SecretGeneratorBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [SecretGenerator::class];
    }

    /**
     * @inheritdoc
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        $eventDispatcher = $container->resolve(IEventDispatcher::class);

        $secretGenerator = new SecretGenerator();
        $eventDispatcher->dispatch(Event::SECRET_GENERATOR_READY, new SecretGeneratorReady($secretGenerator));

        $container->bindInstance(SecretGenerator::class, $secretGenerator);
    }
}
