<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface IAddable extends IStringer
{
    /**
     * @param ...$actions
     *
     * @return $this
     */
    public function add(...$actions): self;
}
