<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid;

use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;

interface IGrid extends IComponent
{
    /**
     * @return int
     */
    public function getPageSize(): int;

    /**
     * @return array
     */
    public function getSortConditions(): array;

    /**
     * @return array
     */
    public function getWhereConditions(): array;

    /**
     * @return array
     */
    public function getSqlParams(): array;

    /**
     * @param int $totalCount
     *
     * @return IGrid
     */
    public function setTotalCount(int $totalCount): IGrid;

    /**
     * @param IStringerEntity[] $entities
     *
     * @return IGrid
     */
    public function setEntities(array $entities): IGrid;
}
