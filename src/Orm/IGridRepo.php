<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Orm;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
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
     * @return IStringerEntity[]
     */
    public function getPage(int $limitFrom, int $pageSize, array $orders, array $conditions, array $params): array;
}
