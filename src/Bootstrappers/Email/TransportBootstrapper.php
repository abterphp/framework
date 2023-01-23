<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Exception\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class TransportBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            TransportInterface::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        try {
            $transport = Transport::fromDsn(Environment::getVar(Env::EMAIL_DNS));
            $container->bindInstance(TransportInterface::class, $transport);
        } catch (\Throwable $e) {
            throw new Config(TransportInterface::class, [Env::EMAIL_DNS], $e->getCode(), $e);
        }
    }
}
