<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Constant\Env;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Transport;

class TransportBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            Swift_Transport::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $transport = null;
        if (getenv(Env::EMAIL_SMTP_HOST)) {
            $transport = $this->createSmtpTransport();
        } elseif (getenv(Env::EMAIL_SENDMAIL_COMMAND)) {
            $transport = $this->createSendmailTransport();
        }

        if (!$transport) {
            throw new \AbterPhp\Framework\Exception\Config(Swift_Transport::class);
        }

        $container->bindInstance(Swift_Transport::class, $transport);
    }

    /**
     * @return Swift_SmtpTransport
     */
    private function createSmtpTransport(): Swift_SmtpTransport
    {
        $host       = (string)getenv(Env::EMAIL_SMTP_HOST);
        $port       = (int)getenv(Env::EMAIL_SMTP_PORT);
        $encryption = (string)getenv(Env::EMAIL_SMTP_ENCRYPTION);

        if (!$encryption) {
            $encryption = null;
        }

        $transport = new Swift_SmtpTransport($host, $port, $encryption);

        $username = (string)getenv(Env::EMAIL_SMTP_USERNAME);
        $password = (string)getenv(Env::EMAIL_SMTP_PASSWORD);

        if ($username && $password) {
            $transport->setUsername($username)->setPassword($password);
        }

        return $transport;
    }

    /**
     * @return Swift_SendmailTransport
     */
    private function createSendmailTransport(): Swift_SendmailTransport
    {
        $command = (string)getenv(Env::EMAIL_SENDMAIL_COMMAND);

        return new Swift_SendmailTransport($command);
    }
}
