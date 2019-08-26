<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use LogicException;
use Opulence\Validation\Rules\MaxRule;

class MaxLength extends MaxRule
{
    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'maxLength';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        if ($this->max === null) {
            throw new LogicException('Maximum value not set');
        }

        if ($this->isInclusive) {
            return mb_strlen($value) <= $this->max;
        } else {
            return mb_strlen($value) > $this->max;
        }
    }
}
