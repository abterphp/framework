<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use PHPUnit\Framework\TestCase;
use Swift_Message;

class MessageFactoryTest extends TestCase
{
    /** @var MessageFactory - System Under Test */
    protected MessageFactory $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new MessageFactory();
    }

    public function testCreate()
    {
        $subject = 'foo';

        $actualResult = $this->sut->create($subject);

        $this->assertInstanceOf(Swift_Message::class, $actualResult);
    }
}
