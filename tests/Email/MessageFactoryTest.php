<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email;

class MessageFactoryTest extends TestCase
{
    /** @var MessageFactory - System Under Test */
    protected MessageFactory $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new MessageFactory();
    }

    public function testCreate(): void
    {
        $subject = 'foo';

        $actualResult = $this->sut->create();

        $this->assertInstanceOf(Email::class, $actualResult);
    }
}
