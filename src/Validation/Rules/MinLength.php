<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use LogicException;
use Opulence\Validation\Rules\MinRule;

class MinLength extends MinRule
{
    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'minLength';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        if ($this->min === null) {
            throw new LogicException('Minimum value not set');
        }

        if ($this->isInclusive) {
            return mb_strlen($value) >= $this->min;
        } else {
            return mb_strlen($value) > $this->min;
        }
    }
}
