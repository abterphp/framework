<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use Swift_Mailer;

class Sender implements ISender
{
    protected Swift_Mailer $mailer;

    protected MessageFactory $messageFactory;

    /** @var string[] */
    protected array $failedRecipients = [];

    /**
     * Sender constructor.
     *
     * @param Swift_Mailer   $mailer
     * @param MessageFactory $messageFactory
     */
    public function __construct(Swift_Mailer $mailer, MessageFactory $messageFactory)
    {
        $this->mailer         = $mailer;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param array  $recipients
     * @param array  $fromAddresses
     * @param array  $replyToAddresses
     *
     * @return int
     */
    public function send(
        string $subject,
        string $body,
        array $recipients,
        array $fromAddresses,
        array $replyToAddresses
    ): int {
        $message = $this->messageFactory
            ->create($subject)
            ->setBody($body)
            ->setFrom($fromAddresses)
            ->setReplyTo($replyToAddresses);

        $this->failedRecipients = [];

        foreach ($recipients as $key => $value) {
            if (is_int($key)) {
                $message->addTo($value);
            } else {
                $message->addTo($key, $value);
            }
        }

        return $this->mailer->send($message, $this->failedRecipients);
    }

    /**
     * @return string[]
     */
    public function getFailedRecipients(): array
    {
        return $this->failedRecipients;
    }
}
