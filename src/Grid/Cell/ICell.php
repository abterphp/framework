<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Html\IComponent;

interface ICell extends IComponent
{
    public function getGroup(): string;
}
