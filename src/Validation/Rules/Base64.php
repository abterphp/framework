<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use Opulence\Validation\Rules\IRule;

class Base64 implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'base64';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        $decoded = base64_decode($value, true);

        if (is_bool($decoded)) {
            return $decoded;
        }

        return true;
    }
}
