<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Constant\Html5;

// TODO: See if refactoring can help with removing suppressed issues
trait TagTrait
{
    protected Attributes $attributes;

    protected string $tag = Html5::TAG_SPAN;

    /**
     * @param string $tag
     *
     * @return INode
     */
    public function setTag(string $tag): INode
    {
        $this->tag = $tag;

        if ($this instanceof INode) {
            return $this;
        }

        return new Node();
    }

    /**
     * @return INode
     */
    public function resetTag(): INode
    {
        if (defined(__CLASS__ . '::DEFAULT_TAG')) {
            $this->tag = static::DEFAULT_TAG; // @phan-suppress-current-line PhanUndeclaredConstantOfClass
        }

        if ($this instanceof INode) {
            return $this;
        }

        return new Node();
    }

    /**
     * @return Attributes
     */
    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param Attributes $attributes
     *
     * @return INode
     */
    public function setAttributes(Attributes $attributes): INode
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param Attributes $attributes
     *
     * @return INode
     */
    public function replaceAttributes(Attributes $attributes): INode
    {
        $this->attributes->replace($attributes);

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param Attributes $attributes
     *
     * @return INode
     */
    public function mergeAttributes(Attributes $attributes): INode
    {
        $this->attributes->merge($attributes);

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param Attribute $attribute
     *
     * @return INode
     */
    public function setAttribute(Attribute $attribute): INode
    {
        $this->attributes->replaceItem($attribute);

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param string $key
     *
     * @return Attribute|null
     */
    public function getAttribute(string $key): ?Attribute
    {
        return $this->attributes->getItem($key);
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        return $this->attributes->hasItem($key);
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param string $key
     *
     * @return Attribute
     */
    public function forceGetAttribute(string $key): Attribute
    {
        if ($this->attributes->hasItem($key)) {
            return $this->attributes->getItem($key);
        }

        $attr = new Attribute($key);

        $this->attributes->replaceItem($attr);

        return $attr;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param string $key
     * @param string ...$values
     *
     * @return INode
     */
    public function appendToAttribute(string $key, string ...$values): INode
    {
        $this->attributes->mergeItem(new Attribute($key, ...$values));

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param string ...$values
     *
     * @return INode
     */
    public function appendToClass(string ...$values): INode
    {
        $this->attributes->mergeItem(new Attribute(Html5::ATTR_CLASS, ...$values));

        return $this;
    }
}
