<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use Symfony\Component\Mime\Address;

interface ISender
{
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
    ): void;
}
