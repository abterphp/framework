<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Html\ITag;

interface IElement extends ITag
{
    /**
     * @param string|array $value
     *
     * @return $this
     */
    public function setValue($value): IElement;

    /**
     * @return $this
     */
    public function getName(): string;
}
