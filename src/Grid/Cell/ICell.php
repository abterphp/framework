<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Html\ITag;

interface ICell extends ITag
{
    public function getGroup(): string;
}
