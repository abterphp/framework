<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Email\MessageFactory;
use AbterPhp\Framework\Email\Sender;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\TransportInterface;

class SenderBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            Sender::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        /** @var TransportInterface $transport */
        $transport = $container->resolve(TransportInterface::class);

        /** @var MessageFactory $messageFactory */
        $messageFactory = $container->resolve(MessageFactory::class);

        $mailer = new Mailer($transport, null, null);

        $sender = new Sender($mailer, $messageFactory);

        $container->bindInstance(Sender::class, $sender);
    }
}
