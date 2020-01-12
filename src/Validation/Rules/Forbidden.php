<?php

namespace AbterPhp\Framework\Validation\Rules;

use Countable;
use Opulence\Validation\Rules\IRule;

/**
 * Defines the required rule
 */
class Forbidden implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'forbidden';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) && $value === '') {
            return true;
        }

        if ((is_array($value) || $value instanceof Countable) && count($value) === 0) {
            return true;
        }

        return false;
    }
}
