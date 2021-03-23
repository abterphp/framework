<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Grid\IGrid;

class GridReady
{
    private IGrid $grid;

    /**
     * GridReady constructor.
     *
     * @param IGrid $grid
     */
    public function __construct(IGrid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return IGrid
     */
    public function getGrid(): IGrid
    {
        return $this->grid;
    }
}
