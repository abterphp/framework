<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use Throwable;

class Exception extends \RuntimeException
{
    /** @var array */
    protected $errorInfo;

    /**
     * Exception constructor.
     *
     * @param array          $errorInfo
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(array $errorInfo, $message = "", $code = 0, Throwable $previous = null)
    {
        if (array_diff(array_keys($errorInfo), [0, 1, 2])) {
            throw new \RuntimeException(sprintf('Invalid errorInfo received for constructing %s.', __CLASS__));
        }

        $this->errorInfo = $errorInfo;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrorInfo(): array
    {
        return $this->errorInfo;
    }
}
