<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\IComponent;

class StringHelper
{
    /**
     * @param string|IComponent $content
     * @param string|null       $tag
     * @param array             $attributes
     *
     * @return string
     */
    public static function wrapInTag($content, string $tag = null, array $attributes = [])
    {
        if (null === $tag) {
            return (string)$content;
        }

        $attributeHtml = ArrayHelper::toAttributes($attributes);

        return sprintf('<%1$s%3$s>%2$s</%1$s>', $tag, (string)$content, $attributeHtml);
    }

    /**
     * @param string $tag
     * @param array  $attributes
     *
     * @return string
     */
    public static function createTag(string $tag, array $attributes = [])
    {
        $attributeHtml = ArrayHelper::toAttributes($attributes);

        return sprintf('<%1$s%2$s>', $tag, $attributeHtml);
    }

    /**
     * @param string $text
     * @param string $tag
     *
     * @return string
     */
    public static function wrapByLines(string $text, string $tag): string
    {
        if (empty($text)) {
            return '';
        }

        $paragraphs = explode(PHP_EOL, $text);

        $lines = [];
        foreach ($paragraphs as $paragraph) {
            if (empty($paragraph)) {
                continue;
            }

            $lines[] = static::wrapInTag(trim($paragraph), $tag);
        }

        return implode(PHP_EOL, $lines);
    }
}
