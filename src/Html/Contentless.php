<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use LogicException;

class Contentless extends Tag
{
    protected const ERROR_NO_CONTENT = 'Contentless can not contain nodes';

    /**
     * Contentless constructor.
     *
     * @param array                        $intents
     * @param array<string,Attribute>|null $attributes
     * @param string|null                  $tag
     */
    public function __construct(array $intents = [], ?array $attributes = null, ?string $tag = null)
    {
        parent::__construct(null, $intents, $attributes, $tag);
    }

    /**
     * @param array<string|IStringer>|string|IStringer|null $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        if (null === $content) {
            return $this;
        }

        throw new LogicException(self::ERROR_NO_CONTENT);
    }
}
