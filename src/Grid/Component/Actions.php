<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\IAction;
use AbterPhp\Framework\Html\Tag;

class Actions extends Tag
{
    protected const DEFAULT_TAG = Html5::TAG_DIV;
    protected const CONTENT_TYPE = IAction::class;

    /** @var IAction[] */
    protected array $content = [];
}
