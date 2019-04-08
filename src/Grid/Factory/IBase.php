<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\IGrid;

interface IBase
{
    /**
     * @param Filters $filters
     *
     * @return IBase
     */
    public function setFilters(Filters $filters): IBase;

    /**
     * @param array  $params
     * @param string $baseUrl
     *
     * @return IGrid
     */
    public function createGrid(array $params, string $baseUrl): IGrid;
}
