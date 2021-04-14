<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface ITag extends INode
{
    /**
     * @param string $tag
     *
     * @return INode
     */
    public function setTag(string $tag): INode;

    /**
     * @return INode
     */
    public function resetTag(): INode;

    /**
     * @return Attributes
     */
    public function getAttributes(): Attributes;

    /**
     * @param Attributes $attributes
     *
     * @return INode
     */
    public function setAttributes(Attributes $attributes): INode;

    /**
     * @param Attributes $attributes
     *
     * @return INode
     */
    public function replaceAttributes(Attributes $attributes): INode;

    /**
     * @param Attributes $attributes
     *
     * @return INode
     */
    public function mergeAttributes(Attributes $attributes): INode;

    /**
     * @param Attribute $attribute
     *
     * @return INode
     */
    public function setAttribute(Attribute $attribute): INode;

    /**
     * @param string $key
     *
     * @return Attribute|null
     */
    public function getAttribute(string $key): ?Attribute;

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
