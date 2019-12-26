<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;

class Button extends Component
{
    protected const DEFAULT_TAG = Html5::TAG_BUTTON;

    public const TYPE_SUBMIT = 'submit';
    public const TYPE_BUTTON = 'button';

    public const INTENT_PRIMARY   = 'primary';
    public const INTENT_SECONDARY = 'secondary';
    public const INTENT_DANGER    = 'danger';
    public const INTENT_SUCCESS   = 'success';
    public const INTENT_INFO      = 'info';
    public const INTENT_WARNING   = 'warning';
    public const INTENT_LINK      = 'link';
    public const INTENT_DEFAULT   = 'default';

    public const INTENT_SMALL = 'small';
    public const INTENT_LARGE = 'large';

    public const INTENT_FAB     = 'fab';
    public const INTENT_FLAT    = 'flat';
    public const INTENT_RAISED  = 'raised';
    public const INTENT_OUTLINE = 'outline';
    public const INTENT_RIPPLE  = 'ripple';

    public const INTENT_FORM = 'form';
}
