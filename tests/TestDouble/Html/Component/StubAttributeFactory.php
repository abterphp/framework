<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestDouble\Html\Component;

use AbterPhp\Framework\Html\Helper\ArrayHelper;

class StubAttributeFactory
{
    public const ATTRIBUTE_FOO = 'foo';
    public const ATTRIBUTE_BAR = 'bar';

    public const VALUE_FOO = 'foo';
    public const VALUE_BAZ = 'baz';
    public const VALUE_BAR_BAZ = 'bar baz';

    /**
     * @param array $extraAttributes
     *
     * @return array
     */
    public static function createAttributes(array $extraAttributes = []): array
    {
        $attributes = [
            static::ATTRIBUTE_FOO => [static::VALUE_FOO, static::VALUE_BAZ],
            static::ATTRIBUTE_BAR => static::VALUE_BAR_BAZ,
        ];

        $attributes = ArrayHelper::unsafeMergeAttributes($attributes, $extraAttributes);

        return $attributes;
    }
}
