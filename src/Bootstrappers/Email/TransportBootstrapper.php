<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Exception;
use Opulence\Environments\Environment;
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
        if (Environment::getVar(Env::EMAIL_SMTP_HOST)) {
            $transport = $this->createSmtpTransport();
        } elseif (Environment::getVar(Env::EMAIL_SENDMAIL_COMMAND)) {
            $transport = $this->createSendmailTransport();
        } else {
            throw new Exception\Config(Swift_Transport::class);
        }

        $container->bindInstance(Swift_Transport::class, $transport);
    }

    /**
     * @return Swift_SmtpTransport
     */
    private function createSmtpTransport(): Swift_SmtpTransport
    {
        $host       = (string)Environment::getVar(Env::EMAIL_SMTP_HOST);
        $port       = (int)Environment::getVar(Env::EMAIL_SMTP_PORT);
        $encryption = (string)Environment::getVar(Env::EMAIL_SMTP_ENCRYPTION);

        if (!$encryption) {
            $encryption = null;
        }

        // @phan-suppress-next-line PhanTypeMismatchArgumentNullable
        $transport = new Swift_SmtpTransport($host, $port, $encryption);

        $username = (string)Environment::getVar(Env::EMAIL_SMTP_USERNAME);
        $password = (string)Environment::getVar(Env::EMAIL_SMTP_PASSWORD);

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
        $command = (string)Environment::getVar(Env::EMAIL_SENDMAIL_COMMAND);

        return new Swift_SendmailTransport($command);
    }
}
