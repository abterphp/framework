<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\StringHelper;

class Tag extends Node implements ITag
{
    const DEFAULT_TAG = Html5::TAG_DIV;

    use TagTrait;

    /**
     * Row constructor.
     *
     * @param mixed       $content
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct($content, $intents);

        $this->setAttributes($attributes);
        $this->setTag($tag);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = $this->translate($this->content);

        return StringHelper::wrapInTag($content, $this->tag, $this->attributes);
    }
}
