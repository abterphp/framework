<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\ArrayHelper;

// TODO: See if refactoring can help with removing suppressed issues
trait TagTrait
{
    /** @var array<string,null|string|string[]|array<string,string>> */
    protected array $attributes = [];

    /** @var string|null */
    protected ?string $tag = null;

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn, PhanUndeclaredConstant, PhanUndeclaredConstantOfClass
     *
     * @param string|null $tag
     *
     * @return INode
     */
    public function setTag(?string $tag = null): INode
    {
        $this->tag = $tag;

        if (!$tag && defined(__CLASS__ . '::DEFAULT_TAG')) {
            $this->tag = static::DEFAULT_TAG;
        }

        if ($this instanceof INode) {
            return $this;
        }

        return new Node();
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * Retrieves all set attributes
     *
     * @return array of strings and nulls
     */
    public function getAttributes(): array
    {
        $result = [];
        foreach (array_keys($this->attributes) as $key) {
            $result[$key] = $this->getAttribute($key);
        }

        return $result;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * Checks if an attribute is set
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        if (!array_key_exists($key, $this->attributes)) {
            return false;
        }

        return true;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * Retrieves a single attribute
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getAttribute(string $key): ?string
    {
        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }

        $attr = $this->attributes[$key];

        if (null === $attr) {
            return null;
        }

        $attr = (array)$attr;

        return implode(' ', $attr);
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * Unsets a single attribute
     *
     * @param string $key
     *
     * @return INode
     */
    public function unsetAttribute(string $key): INode
    {
        if (!array_key_exists($key, $this->attributes)) {
            return $this;
        }

        unset($this->attributes[$key]);

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn, PhanTypeMismatchArgumentNullableInternal
     *
     * Removes a single attribute value
     *
     * @param string $key
     * @param string $value
     *
     * @return INode
     */
    public function unsetAttributeValue(string $key, string $value): INode
    {
        if (!array_key_exists($key, $this->attributes)) {
            return $this;
        }

        if (!is_array($this->attributes[$key]) || !array_key_exists($value, $this->attributes[$key])) {
            return $this;
        }

        unset($this->attributes[$key][$value]);

        if (empty($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * Unsets all existing attributes and replaces them with the newly provided attributes
     * Use addAttributes if you want to keep all existing attributes but the ones provided
     *
     * @param array $attributes
     *
     * @return INode
     */
    public function setAttributes(array $attributes): INode
    {
        $this->attributes = [];
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = ArrayHelper::formatAttribute($value);
        }

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * Replaces all set provided attributes with new ones
     * Existing ones will be kept if not provided
     *
     * @param array $attributes
     *
     * @return INode
     */
    public function addAttributes(array $attributes): INode
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = ArrayHelper::formatAttribute($value);
        }

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
     * @param string      $key
     * @param string|null ...$values
     *
     * @return INode
     */
    public function setAttribute(string $key, ?string ...$values): INode
    {
        $this->attributes[$key] = ArrayHelper::formatAttribute($values);

        return $this;
    }

    /**
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn, PhanTypeMismatchArgumentNullableInternal
     *
     * @param array $attributes
     *
     * @return INode
     */
    public function appendToAttributes(array $attributes): INode
    {
        foreach ($attributes as $key => $values) {
            if (!isset($this->attributes[$key])) {
                $this->attributes[$key] = [];
            }

            $newValues = ArrayHelper::formatAttribute($values);
            if ($newValues === null) {
                $this->attributes[$key] = null;
                continue;
            }

            $this->attributes[$key] = array_merge($this->attributes[$key], $newValues);
        }

        return $this;
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
        return $this->appendToAttributes([$key => $values]);
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
        if (empty($values)) {
            return $this;
        }

        return $this->appendToAttribute(Html5::ATTR_CLASS, ...$values);
    }
}
