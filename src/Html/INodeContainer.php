<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface INodeContainer
{
    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array;

    /**
     * @return INode[]
     */
    public function getNodes(): array;

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getExtendedDescendantNodes(int $depth = -1): array;

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getDescendantNodes(int $depth = -1): array;
}
