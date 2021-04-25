<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface IStringer
{
    /**
     * @return string
     */
    public function __toString(): string;
}
