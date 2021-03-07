<?php

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Exception\Config;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Transport;

class TransportBootstrapperTest extends TestCase
{
    /** @var TransportBootstrapper */
    protected TransportBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new TransportBootstrapper();
    }

    public function tearDown(): void
    {
        Environment::unsetVar(Env::EMAIL_SMTP_HOST);
        Environment::unsetVar(Env::EMAIL_SMTP_PORT);
        Environment::unsetVar(Env::EMAIL_SMTP_ENCRYPTION);
        Environment::unsetVar(Env::EMAIL_SMTP_USERNAME);
        Environment::unsetVar(Env::EMAIL_SMTP_PASSWORD);
        Environment::unsetVar(Env::EMAIL_SENDMAIL_COMMAND);
    }

    public function testRegisterBindingsSmtp()
    {
        Environment::setVar(Env::EMAIL_SMTP_HOST, 'foo');
        Environment::setVar(Env::EMAIL_SMTP_PORT, 'bar');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Swift_Transport::class);
        $this->assertInstanceOf(Swift_SmtpTransport::class, $actual);
    }

    public function testRegisterBindingsSmtpSetsUserNameAndPassword()
    {
        Environment::setVar(Env::EMAIL_SMTP_HOST, 'foo');
        Environment::setVar(Env::EMAIL_SMTP_PORT, 'bar');
        Environment::setVar(Env::EMAIL_SMTP_ENCRYPTION, 'baz');
        Environment::setVar(Env::EMAIL_SMTP_USERNAME, 'quix');
        Environment::setVar(Env::EMAIL_SMTP_PASSWORD, 'sterp');

        $container = new Container();

        $this->sut->registerBindings($container);

        /** @var Swift_SmtpTransport $actual */
        $actual = $container->resolve(Swift_Transport::class);
        $this->assertInstanceOf(Swift_SmtpTransport::class, $actual);
        $this->assertSame('quix', $actual->getUsername());
        $this->assertSame('sterp', $actual->getPassword());
    }

    public function testRegisterBindingsSendmail()
    {
        Environment::setVar(Env::EMAIL_SENDMAIL_COMMAND, 'foo');

        $container = new Container();

        $this->sut->registerBindings($container);

        /** @var Swift_SmtpTransport $actual */
        $actual = $container->resolve(Swift_Transport::class);
        $this->assertInstanceOf(Swift_SendmailTransport::class, $actual);
    }

    public function testRegisterBindingsTransport()
    {
        $this->expectException(Config::class);

        $container = new Container();

        $this->sut->registerBindings($container);
    }
}
