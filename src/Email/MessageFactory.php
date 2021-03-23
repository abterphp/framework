<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use Swift_Message;

class MessageFactory
{
    /**
     * @param string $subject
     *
     * @return Swift_Message
     */
    public function create(string $subject): Swift_Message
    {
        return new Swift_Message($subject);
    }
}
