<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;

class Help extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_DIV;

    public const CLASS_HELP_BLOCK = 'help-block';

    /** @var array */
    protected $attributes = [
        Html5::ATTR_CLASS => [self::CLASS_HELP_BLOCK],
    ];
}
