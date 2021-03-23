<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Collection;

use AbterPhp\Framework\Grid\Cell\ICell;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\INode;

class Cells extends Collection
{
    /** @var ICell[] */
    protected array $nodes = [];

    /** @var string */
    protected string $nodeClass = ICell::class;

    /**
     * @param string $content
     *
     * @return INode
     */
    protected function createNode(string $content): INode
    {
        throw new \LogicException();
    }
}
