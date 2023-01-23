<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Email\MessageFactory;
use AbterPhp\Framework\Email\Sender;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class SenderBootstrapperTest extends TestCase
{
    /** @var SenderBootstrapper - System Under Test */
    protected SenderBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new SenderBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $nullTransport      = Transport::fromDsn('null://null');
        $messageFactoryMock = $this->getMockBuilder(MessageFactory::class)->getMock();

        $container = new Container();
        $container->bindInstance(TransportInterface::class, $nullTransport);
        $container->bindInstance(MessageFactory::class, $messageFactoryMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Sender::class);
        $this->assertInstanceOf(Sender::class, $actual);
    }
}
