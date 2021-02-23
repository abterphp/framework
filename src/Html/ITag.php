<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface ITag extends INode
{
    /**
     * @param string|null $tag
     *
     * @return INode
     */
    public function setTag(?string $tag = null): INode;

    /**
     * Retrieves all set attributes
     *
     * @return array of strings and nulls
     */
    public function getAttributes(): array;

    /**
     * Checks if an attribute is set
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key): bool;

    /**
     * Retrieves a single attribute
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getAttribute(string $key): ?string;

    /**
     * Unsets a single attribute
     *
     * @param string $key
     *
     * @return INode
     */
    public function unsetAttribute(string $key): INode;

    /**
     * Removes a single attribute value
     *
     * @param string $key
     * @param string $value
     *
     * @return INode
     */
    public function unsetAttributeValue(string $key, string $value): INode;

    /**
     * Unsets all existing attributes and replaces them with the newly provided attributes
     * Use addAttributes if you want to keep all existing attributes but the ones provided
     *
     * @param array $attributes
     *
     * @return INode
     */
    public function setAttributes(array $attributes): INode;

    /**
     * Replaces all set provided attributes with new ones
     * Existing ones will be kept if not provided
     *
     * @param array $attributes
     *
     * @return INode
     */
    public function addAttributes(array $attributes): INode;

    /**
     * @param string      $key
     * @param string|null ...$values
     *
     * @return INode
     */
    public function setAttribute(string $key, ?string ...$values): INode;

    /**
     * @param array $attributes
     *
     * @return INode
     */
    public function appendToAttributes(array $attributes): INode;

    /**
     * @param string $key
     * @param string ...$values
     *
     * @return INode
     */
    public function appendToAttribute(string $key, string ...$values): INode;

    /**
     * @param string ...$values
     *
     * @return INode
     */
    public function appendToClass(string ...$values): INode;
}
