<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Message;

class SenderTest extends TestCase
{
    /** @var Sender System Under Test */
    protected $sut;

    /** @var Swift_Mailer|MockObject */
    protected $mailerMock;

    /** @var Swift_Message|MockObject */
    protected $messageMock;

    /** @var MessageFactory|MockObject */
    protected $messageFactory;

    /** @var array */
    protected $recipients = ['john@example.com', 'jane' => 'jane@example.com'];

    public function setUp()
    {
        parent::setUp();

        $this->messageMock = $this->getMockBuilder(Swift_Message::class)
            ->disableOriginalConstructor()
            ->setMethods(['setBody', 'setFrom', 'setReplyTo', 'addTo'])
            ->getMock();
        $this->messageMock->expects($this->any())->method('setBody')->willReturnSelf();
        $this->messageMock->expects($this->any())->method('setFrom')->willReturnSelf();
        $this->messageMock->expects($this->any())->method('setReplyTo')->willReturnSelf();

        $this->messageFactory = $this->getMockBuilder(MessageFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->messageFactory->expects($this->any())->method('create')->willReturn($this->messageMock);

        $this->mailerMock = $this->getMockBuilder(Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $this->sut = new Sender($this->mailerMock, $this->messageFactory);
    }

    public function testSend()
    {
        $this->mailerMock->expects($this->once())->method('send')->willReturn(count($this->recipients));

        $senders = ['sender@example.com'];
        $replyTo = ['no-reply@example.com'];
        $subject = 'foo';
        $body    = 'bar';

        $this->sut->send($subject, $body, $this->recipients, $senders, $replyTo);
    }

    public function testGetFailedRecipientsIsEmptyByDefault()
    {
        $recipients = $this->sut->getFailedRecipients();

        $this->assertSame([], $recipients);
    }
}
