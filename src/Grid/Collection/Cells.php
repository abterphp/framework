<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Collection;

use AbterPhp\Framework\Grid\Cell\ICell;
use AbterPhp\Framework\Html\Tag;

class Cells extends Tag
{
    protected const CONTENT_TYPE = ICell::class;

    /** @var ICell[] */
    protected array $content = [];
}
