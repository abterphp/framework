<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use Opulence\Validation\Rules\IRule;

class Url implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'url';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}
