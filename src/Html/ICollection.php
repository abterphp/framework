<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use ArrayAccess;
use Countable;
use Iterator;

interface ICollection extends ArrayAccess, Countable, Iterator, INode, INodeContainer
{
    /**
     * @param INode $nodeToFind
     * @param INode ...$nodes
     *
     * @return bool
     */
    public function insertBefore(INode $nodeToFind, INode... $nodes): bool;

    /**
     * @param INode $nodeToFind
     * @param INode ...$nodes
     *
     * @return bool
     */
    public function insertAfter(INode $nodeToFind, INode... $nodes): bool;

    /**
     * @param INode $nodeToFind
     * @param INode ...$nodes
     *
     * @return bool
     */
    public function replace(INode $nodeToFind, INode... $nodes): bool;

    /**
     * @param INode $node
     * @param int   $maxDepth
     *
     * @return bool
     */
    public function remove(INode $node): bool;
}
