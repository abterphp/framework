<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use AbterPhp\Framework\Html\Attribute;

class Tag
{
    protected const SINGLE_TAGS      = ['br', 'hr', 'img', 'input', 'link', 'meta', 'source'];
    protected const NO_CONTENT_TAG   = '<%1$s%2$s>';
    protected const WITH_CONTENT_TAG = '<%1$s%3$s>%2$s</%1$s>';

    /**
     * @param string                  $tag
     * @param string                  $content
     * @param array<string,Attribute> $attributes
     *
     * @return string
     */
    public static function toString(string $tag, string $content, array $attributes): string
    {
        $attributes = Attributes::toString($attributes);

        if (in_array($tag, self::SINGLE_TAGS, true)) {
            return sprintf(self::NO_CONTENT_TAG, $tag, $attributes);
        }

        return sprintf(self::WITH_CONTENT_TAG, $tag, $content, $attributes);
    }
}
