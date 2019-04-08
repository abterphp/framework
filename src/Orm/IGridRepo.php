<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Orm;

use Opulence\Orm\Repositories\IRepository;

interface IGridRepo extends IRepository
{
    /**
     * @param int   $limitFrom
     * @param int   $pageSize
     * @param array $orders
     * @param array $conditions
     * @param array $params
     *
     * @return mixed
     */
    public function getPage(int $limitFrom, int $pageSize, array $orders, array $conditions, array $params): array;
}
