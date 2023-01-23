<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SenderTest extends TestCase
{
    /** @var Sender - System Under Test */
    protected Sender $sut;

    protected Mailer $fakeMailer;

    protected Email|MockObject $emailMock;

    protected MessageFactory|MockObject $messageFactory;

    protected array $recipients = [
        'john@example.com',
        'jane' => 'jane@example.com'
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->emailMock = $this->createMock(Email::class);

        $this->messageFactory = $this->createMock(MessageFactory::class);
        $this->messageFactory->expects($this->any())->method('create')->willReturn($this->emailMock);

        $this->fakeMailer = new Mailer(Transport::fromDsn('null://null'));

        $this->sut = new Sender($this->fakeMailer, $this->messageFactory);
    }

    public function testSendToNobody(): void
    {
        $this->emailMock->expects($this->once())->method('from')->willReturnSelf();
        $this->emailMock->expects($this->once())->method('subject')->willReturnSelf();
        $this->emailMock->expects($this->once())->method('text')->willReturnSelf();
        $this->emailMock->expects($this->once())->method('html')->willReturnSelf();
        $this->emailMock->expects($this->never())->method('to')->willReturnSelf();

        $sender   = new Address('sender@example.com');
        $subject  = 'foo';
        $bodyText = 'bar';
        $bodyHtml = '<b>bar</b>';

        $recipients = [];

        $this->sut->send($subject, $bodyText, $bodyHtml, $sender, $recipients);
    }

    public function testSend(): void
    {
        $this->emailMock->expects($this->once())->method('from')->willReturnSelf();
        $this->emailMock->expects($this->once())->method('subject')->willReturnSelf();
        $this->emailMock->expects($this->once())->method('text')->willReturnSelf();
        $this->emailMock->expects($this->once())->method('html')->willReturnSelf();
        $this->emailMock->expects($this->exactly(2))->method('to')->willReturnSelf();

        $sender   = new Address('sender@example.com');
        $subject  = 'foo';
        $bodyText = 'bar';
        $bodyHtml = '<b>bar</b>';

        $recipients = [];
        foreach ($this->recipients as $key => $value) {
            if (is_int($key)) {
                $recipients[] = new Address($value);
            } else {
                $recipients[] = new Address($value, $key);
            }
        }

        $this->sut->send($subject, $bodyText, $bodyHtml, $sender, $recipients);
    }

    public function testGetFailedRecipientsIsEmptyByDefault(): void
    {
        $recipients = $this->sut->getFailedRecipients();

        $this->assertSame([], $recipients);
    }
}
