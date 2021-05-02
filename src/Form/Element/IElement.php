<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Html\ITag;

interface IElement extends ITag
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|string[]
     */
    public function getValue();

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): IElement;
}
