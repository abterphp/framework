<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use Throwable;

class Exception extends \RuntimeException
{
    /**
     * Exception constructor.
     *
     * @param string[]       $errorInfo
     * @param string         $query
     * @param string|null    $className
     * @param string|null    $fileName
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $errorInfo,
        string $query,
        ?string $className = null,
        ?string $fileName = null,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        if (array_diff(array_keys($errorInfo), [0, 1, 2])) {
            throw new \RuntimeException(sprintf('Invalid errorInfo received for constructing %s.', __CLASS__));
        }

        $message = $this->customMessage($errorInfo, $query, $className, $fileName, $message);

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string[]    $errorInfo
     * @param string      $query
     * @param string|null $className
     * @param string|null $fileName
     * @param string      $message
     *
     * @return string
     */
    private function customMessage($errorInfo, $query, $className, $fileName, $message): string
    {
        $pieces   = [];
        $pieces[] = sprintf('Class: %s', $className ?: '(missing)');
        $pieces[] = sprintf('File: %s', $fileName ?: '(missing)');
        $pieces[] = sprintf('Query: %s', $query);
        if ($message) {
            $pieces[] = sprintf('Original message: %s', $message);
        }

        $pieces[] = sprintf('SqlState error code: %s', $errorInfo[0]);
        $pieces[] = sprintf('Driver error code: %s', $errorInfo[1]);
        $pieces[] = sprintf('Driver error message: %s', $errorInfo[2]);

        return join("\n", $pieces);
    }
}
