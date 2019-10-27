<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class Factory
{
    /**
     * @param string $rawContent
     *
     * @return Template
     */
    public function create(string $rawContent = ''): Template
    {
        return new Template($rawContent);
    }
}
