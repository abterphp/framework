<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Html\ITag;

interface IElement extends ITag
{
    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): IElement;

    /**
     * @return string
     */
    public function getName(): string;
}
