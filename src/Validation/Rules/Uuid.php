<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use Opulence\Validation\Rules\IRule;

/**
 * Uuid validation based on Uuid validation in Zend Framework 2
 *
 * @see https://github.com/zendframework/zend-validator/blob/master/src/Uuid.php
 */
class Uuid implements IRule
{
    /**
     * Matches Uuid's versions 1 to 5.
     */
    protected const REGEX_UUID = '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/';

    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'uuid';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => static::REGEX_UUID]]) !== false;
    }
}
