<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\TagHelper;

class Tag extends Node implements ITag
{
    use TagTrait;

    protected const DEFAULT_TAG = Html5::TAG_DIV;

    /**
     * Row constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param Attributes|null           $attributes
     * @param string|null               $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        ?Attributes $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct($content, $intents);

        $this->attributes = $attributes ?? new Attributes();

        if ($tag) {
            $this->setTag($tag);
        } else {
            $this->resetTag();
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = $this->translate($this->content);

        return TagHelper::toString($this->tag, $content, $this->attributes);
    }
}
