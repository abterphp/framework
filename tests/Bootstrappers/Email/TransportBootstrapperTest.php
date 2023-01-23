<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Exception\Config;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Transport\TransportInterface;

class TransportBootstrapperTest extends TestCase
{
    /** @var TransportBootstrapper - System Under Test */
    protected TransportBootstrapper $sut;

    public function setUp(): void
    {
        Environment::unsetVar(Env::EMAIL_DNS);

        $this->sut = new TransportBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        Environment::setVar(Env::EMAIL_DNS, 'null://null');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(TransportInterface::class);
        $this->assertInstanceOf(TransportInterface::class, $actual);
    }


    public function testRegisterBindingsTransport(): void
    {
        $this->expectException(Config::class);

        $container = new Container();

        $this->sut->registerBindings($container);
    }
}
