<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

class TagHelper
{
    protected const SINGLE_TAGS      = ['br', 'hr', 'img', 'input', 'link', 'meta', 'source'];
    protected const NO_CONTENT_TAG   = '<%1$s%2$s>';
    protected const WITH_CONTENT_TAG = '<%1$s%3$s>%2$s</%1$s>';

    public static function toString(string $tag, string $content, $attributes): string
    {
        if (null === $attributes) {
            $attributes = '';
        }

        if (!$content && in_array($tag, self::SINGLE_TAGS, true)) {
            return sprintf(self::NO_CONTENT_TAG, $tag, (string)$attributes);
        }

        return sprintf(self::WITH_CONTENT_TAG, $tag, $content, (string)$attributes);
    }
}
