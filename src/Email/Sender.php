<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use Swift_Mailer;
use Swift_Message;

class Sender
{
    /** @var Swift_Mailer */
    protected $mailer;

    /** @var MessageFactory */
    protected $messageFactory;

    /** @var array */
    protected $failedRecipients = [];

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
     * @param array  $senders
     * @param array  $replyTo
     *
     * @return int
     */
    public function send(string $subject, string $body, array $recipients, array $senders, array $replyTo): int
    {
        $message = $this->messageFactory->create($subject)->setBody($body)->setFrom($senders)->setReplyTo($replyTo);

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
     * @return array
     */
    public function getFailedRecipients(): array
    {
        return $this->failedRecipients;
    }
}
