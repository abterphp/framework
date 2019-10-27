<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Html\Helper\StringHelper;

final class Contentless extends Component
{
    private const ERROR_NO_CONTENT = 'Contentless can not contain nodes';

    /**
     * Contentless constructor.
     *
     * @param array       $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(array $intents = [], array $attributes = [], ?string $tag = null)
    {
        parent::__construct(null, $intents, $attributes, $tag);
    }

    /**
     * @param INode $node
     *
     * @return int|null
     */
    public function find(INode $node): ?int
    {
        throw new \LogicException(static::ERROR_NO_CONTENT);
    }

    /**
     * Collects all children, grandchildren, etc that match the arguments provided
     *
     * @param string|null $className
     * @param array       $intents
     * @param int         $depth maximum level of recursion, -1 or smaller means infinite, 0 is direct children only
     *
     * @return IComponent[]
     */
    public function collect(?string $className = null, array $intents = [], int $depth = -1): array
    {
        throw new \LogicException(static::ERROR_NO_CONTENT);
    }

    /**
     * Tries to find the first child that matches the arguments provided
     *
     * @param string|null $className
     * @param string      ...$intents
     *
     * @return IComponent|null
     */
    public function findFirstChild(?string $className = null, string ...$intents): ?IComponent
    {
        throw new \LogicException(static::ERROR_NO_CONTENT);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return $this
     */
    public function setContent($content = null): INode
    {
        if (null === $content) {
            return $this;
        }
        
        throw new \LogicException(static::ERROR_NO_CONTENT);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = StringHelper::createTag($this->tag, $this->attributes);

        return $content;
    }
}
