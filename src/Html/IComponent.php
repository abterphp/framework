<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface IComponent extends ICollection, ITag
{
    /**
     * @param INode $node
     *
     * @return int|null
     */
    public function find(INode $node): ?int;

    /**
     * Tries to find the first child that matches the arguments provided
     *
     * @param string|null $className
     * @param string      ...$intents
     *
     * @return IComponent|null
     */
    public function findFirstChild(?string $className = null, string ...$intents): ?IComponent;

    /**
     * Collects all children, grandchildren, etc that match the arguments provided
     *
     * @param string|null $className
     * @param array       $intents
     * @param int         $depth maximum level of recursion, -1 or smaller means infinite, 0 is direct children only
     *
     * @return IComponent[]
     */
    public function collect(?string $className = null, array $intents = [], int $depth = -1): array;
}
