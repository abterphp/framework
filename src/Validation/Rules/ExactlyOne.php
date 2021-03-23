<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use InvalidArgumentException;
use Opulence\Validation\Rules\IRuleWithArgs;
use Opulence\Validation\Rules\IRuleWithErrorPlaceholders;

class ExactlyOne implements IRuleWithArgs, IRuleWithErrorPlaceholders
{
    /** @var string[] The name of the fields to compare to */
    protected array $fieldNames = [];

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
        return 'exactlyOne';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        $count = 0;

        if (is_string($value) && $value) {
            $count++;
        }

        foreach ($this->fieldNames as $fieldName) {
            if (isset($allValues[$fieldName]) && is_string($allValues[$fieldName]) && $allValues[$fieldName]) {
                $count++;
            }
        }

        return $count === 1;
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
