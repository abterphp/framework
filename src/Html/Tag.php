<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\Collection;
use AbterPhp\Framework\Html\Helper\Tag as Helper;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Tag extends Node implements ITag
{
    protected const DEFAULT_TAG = Html5::TAG_DIV;

    protected const PROTECTED_KEYS = [];

    /** @var array<string,Attribute> */
    protected array $attributes = [];

    protected string $tag = Html5::TAG_DIV;

    /**
     * Tag constructor.
     *
     * @param array<string|INode>|string|INode|null $content
     * @param string[]                              $intents
     * @param array<string,Attribute>|null          $attributes
     * @param string|null                           $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        ?array $attributes = null,
        ?string $tag = null
    ) {
        parent::__construct($content, ...$intents);

        $attributes ??= [];
        $tag        ??= static::DEFAULT_TAG;

        $this->setAttributes($attributes);
        $this->setTag($tag);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = Node::__toString();

        return Helper::toString($this->tag, $content, $this->attributes);
    }

    /**
     * @param string $tag
     *
     * @return $this
     */
    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return $this
     */
    public function resetTag(): self
    {
        $this->tag = static::DEFAULT_TAG;

        return $this;
    }

    /**
     * @return array<string,Attribute>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string,Attribute> $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        assert(Collection::allInstanceOf($attributes, Attribute::class));

        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return Attribute|null
     */
    public function getAttribute(string $key): ?Attribute
    {
        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * @param Attribute ...$attributes
     *
     * @return $this
     */
    public function setAttribute(Attribute ...$attributes): self
    {
        foreach ($attributes as $attribute) {
            $this->attributes[$attribute->getKey()] = $attribute;
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute(string $key): self
    {
        if (in_array($key, static::PROTECTED_KEYS)) {
            throw new \RuntimeException(sprintf("Attribute is protected, can not be removed: %s", $key));
        }

        unset($this->attributes[$key]);

        return $this;
    }

    /**
     * @param string $key
     * @param string ...$values
     *
     * @return $this
     */
    public function appendToAttribute(string $key, string ...$values): self
    {
        if (!array_key_exists($key, $this->attributes)) {
            $this->attributes[$key] = new Attribute($key, ...$values);

            return $this;
        }

        $this->attributes[$key]->append(...$values);

        return $this;
    }

    /**
     * @param string ...$values
     *
     * @return $this
     */
    public function appendToClass(string ...$values): self
    {
        return $this->appendToAttribute(Html5::ATTR_CLASS, ...$values);
    }
}
