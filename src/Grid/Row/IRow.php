<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Row;

use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Html\ITag;
use Opulence\Orm\IEntity;

interface IRow extends ITag
{
    public function getCells(): Cells;

    public function setEntity(IEntity $entity);

    public function getEntity(): IEntity;
}
