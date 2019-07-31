<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Email\MessageFactory;
use AbterPhp\Framework\Email\Sender;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Swift_Mailer;
use Swift_Transport;

class SenderBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
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
    public function registerBindings(IContainer $container)
    {
        /** @var Swift_Transport $transport */
        $transport = $container->resolve(Swift_Transport::class);

        /** @var MessageFactory $messageFactory */
        $messageFactory = $container->resolve(MessageFactory::class);

        $mailer = new Swift_Mailer($transport);

        $sender = new Sender($mailer, $messageFactory);

        $container->bindInstance(Sender::class, $sender);
    }
}
