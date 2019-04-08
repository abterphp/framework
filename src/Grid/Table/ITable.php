<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Table;

use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;

interface ITable extends IComponent
{
    /**
     * @return string
     */
    public function getSortedUrl(string $baseUrl): string;

    /**
     * @return array
     */
    public function getSortConditions(): array;

    /**
     * @return array
     */
    public function getSqlParams(): array;

    /**
     * @param IStringerEntity[] $entities
     *
     * @return ITable
     */
    public function setEntities(array $entities): ITable;
}
