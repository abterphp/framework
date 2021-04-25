<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface ITag extends INode
{
    /**
     * @param string $tag
     *
     * @return $this
     */
    public function setTag(string $tag): INode;

    /**
     * @return $this
     */
    public function resetTag(): INode;

    /**
     * @return array<string,Attribute>
     */
    public function getAttributes(): array;

    /**
     * @param array<string,Attribute> $attributes
     *
     * @return INode
     */
    public function setAttributes(array $attributes): INode;

    /**
     * @param Attribute ...$attributes
     *
     * @return INode
     */
    public function setAttribute(Attribute ...$attributes): INode;

    /**
     * @param string $key
     *
     * @return Attribute|null
     */
    public function getAttribute(string $key): ?Attribute;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key): bool;

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute(string $key): self;

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
