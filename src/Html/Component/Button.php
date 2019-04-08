<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;

class Button extends Component
{
    const DEFAULT_TAG = Html5::TAG_BUTTON;

    const TYPE_SUBMIT = 'submit';
    const TYPE_BUTTON = 'button';

    const INTENT_PRIMARY   = 'primary';
    const INTENT_SECONDARY = 'secondary';
    const INTENT_DANGER    = 'danger';
    const INTENT_SUCCESS   = 'success';
    const INTENT_INFO      = 'info';
    const INTENT_WARNING   = 'warning';
    const INTENT_LINK      = 'link';

    const INTENT_SMALL = 'small';
    const INTENT_LARGE = 'large';

    const INTENT_FORM = 'form';

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = parent::__toString();

        return $content;
    }
}
