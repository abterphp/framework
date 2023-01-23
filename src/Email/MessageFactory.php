<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Email;

use Symfony\Component\Mime\Email;

class MessageFactory
{
    /**
     * @return Email
     */
    public function create(): Email
    {
        return new Email();
    }
}
