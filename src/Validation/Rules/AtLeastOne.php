<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use InvalidArgumentException;
use Opulence\Validation\Rules\IRuleWithArgs;
use Opulence\Validation\Rules\IRuleWithErrorPlaceholders;

class AtLeastOne implements IRuleWithArgs, IRuleWithErrorPlaceholders
{
    /** @var array The name of the fields to compare to */
    protected $fieldNames = [];

    /**
     * @inheritdoc
     */
    public function getErrorPlaceholders(): array
    {
        $placeholders = [];

        foreach ($this->fieldNames as $idx => $fieldName) {
            $placeholders['other' . ($idx + 1)] = $fieldName;
        }

        return $placeholders;
    }

    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'atLeastOne';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        if (is_string($value) && $value) {
            return true;
        }

        foreach ($this->fieldNames as $fieldName) {
            if (isset($allValues[$fieldName]) && is_string($allValues[$fieldName]) && $allValues[$fieldName]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) < 1) {
            throw new InvalidArgumentException('Must pass valid field names');
        }

        foreach ($args as $arg) {
            if (!is_string($arg)) {
                throw new InvalidArgumentException('Must pass valid field names');
            }
        }

        $this->fieldNames = $args;
    }
}
