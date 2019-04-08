<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Exception;

use Throwable;

class Config extends \LogicException
{
    const ERROR_MSG        = 'Insufficient configs found while dependency: %s';
    const REQUIRED_CONFIGS = ' (Related environment variables: %s)';

    /**
     * Config constructor.
     *
     * @param string         $className
     * @param array          $relatedEnvVars
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $className,
        array $relatedEnvVars = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = sprintf(static::ERROR_MSG, $className);

        if (!empty($relatedEnvVars)) {
            $message .= sprintf(static::REQUIRED_CONFIGS, implode(', ', $relatedEnvVars));
        }

        parent::__construct($message, $code, $previous);
    }
}
