<?php

namespace AbterPhp\Framework\Bootstrappers\Email;

use AbterPhp\Framework\Email\MessageFactory;
use AbterPhp\Framework\Email\Sender;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;
use Swift_Transport;

class SenderBootstrapperTest extends TestCase
{
    /** @var SenderBootstrapper - System Under Test */
    protected SenderBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new SenderBootstrapper();
    }

    public function testRegisterBindings()
    {
        $transportMock      = $this->getMockBuilder(Swift_Transport::class)->getMock();
        $messageFactoryMock = $this->getMockBuilder(MessageFactory::class)->getMock();

        $container = new Container();
        $container->bindInstance(Swift_Transport::class, $transportMock);
        $container->bindInstance(MessageFactory::class, $messageFactoryMock);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(Sender::class);
        $this->assertInstanceOf(Sender::class, $actual);
    }
}
