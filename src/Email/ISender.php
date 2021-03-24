<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

interface ISender
{
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
    ): int;
}
