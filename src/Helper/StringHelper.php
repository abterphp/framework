<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

class StringHelper
{
    /**
     * @param string $plainText
     * @param string $prefix
     * @param string $postfix
     *
     * @return string
     */
    public static function plainToHtml(string $plainText, string $prefix = '', string $postfix = ''): string
    {
        if (empty($plainText)) {
            return '';
        }

        $paragraphs = explode("\n", $plainText);

        $lines = [];
        foreach ($paragraphs as $paragraph) {
            if (empty($paragraph)) {
                continue;
            }
            $lines[] = "$prefix$paragraph$postfix";
        }

        return implode("\n", $lines);
    }
}
