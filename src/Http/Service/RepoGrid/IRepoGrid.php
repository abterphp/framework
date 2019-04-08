<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Service\RepoGrid;

use AbterPhp\Framework\Grid\IGrid;
use Opulence\Http\Collection;

interface IRepoGrid
{
    /**
     * @param Collection $query
     * @param string     $baseUrl
     *
     * @return IGrid
     */
    public function createAndPopulate(Collection $query, string $baseUrl): IGrid;
}
