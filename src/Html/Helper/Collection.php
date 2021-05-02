<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use AbterPhp\Framework\Html\INode;

class Collection
{
    protected const ERROR_INVALID_ATTRIBUTES = 'invalid attributes (array of string[] and null items)';

    /**
     * @param array  $items
     * @param string $className
     *
     * @return bool
     */
    public static function allInstanceOf(array $items, string $className): bool
    {
        if (empty($className)) {
            return true;
        }

        foreach ($items as $item) {
            if (!($item instanceof $className)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $items
     *
     * @return bool
     */
    public static function allNodes(array $items): bool
    {
        foreach ($items as $item) {
            if (!is_scalar($item) && !($item instanceof INode)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[] $items
     *
     * @return bool
     */
    public static function allStrings(array $items): bool
    {
        foreach ($items as $item) {
            if (!is_string($item)) {
                return false;
            }
        }

        return true;
    }
}
