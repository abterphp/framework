<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;

class Sender implements ISender
{
    protected Mailer $mailer;

    protected MessageFactory $messageFactory;

    /** @var Address[] */
    protected array $failedRecipients = [];

    /**
     * Sender constructor.
     *
     * @param Mailer         $mailer
     * @param MessageFactory $messageFactory
     */
    public function __construct(Mailer $mailer, MessageFactory $messageFactory)
    {
        $this->mailer         = $mailer;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param string       $subject
     * @param string       $textBody
     * @param string       $htmlBody
     * @param Address      $sender
     * @param Address[]    $recipients
     * @param Address|null $replyToAddress
     * @param int|null     $priority
     * @param array|null   $bcc
     * @param array|null   $cc
     *
     * @return void
     */
    public function send(
        string $subject,
        string $textBody,
        string $htmlBody,
        Address $sender,
        array $recipients,
        ?Address $replyToAddress = null,
        ?int $priority = null,
        ?array $bcc = null,
        ?array $cc = null,
    ): void {
        $message = $this->messageFactory->create()
            ->from($sender)
            ->subject($subject)
            ->text($textBody)
            ->html($htmlBody);

        if ($replyToAddress !== null) {
            $message->replyTo($replyToAddress);
        }

        if ($priority !== null) {
            $message->priority($priority);
        }

        if ($cc !== null) {
            $message->cc(...$cc);
        }

        if ($bcc !== null) {
            $message->bcc(...$bcc);
        }

        $this->failedRecipients = [];

        foreach ($recipients as $value) {
            $message->to($value);

            try {
                $this->mailer->send($message);
            } catch (\Throwable $e) {
                $this->failedRecipients[] = $value;
            }
        }
    }

    /**
     * @return Address[]
     */
    public function getFailedRecipients(): array
    {
        return $this->failedRecipients;
    }
}
