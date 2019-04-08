<?php

namespace AbterPhp\Framework\Debug\Exceptions\Handlers\Whoops;

use Exception;
use Opulence\Framework\Debug\Exceptions\Handlers\Http;
use Throwable;
use Whoops\Run;

/**
 * @SuppressWarnings(PHPMD)
 */
class ExceptionRenderer extends Http\ExceptionRenderer implements Http\IExceptionRenderer
{
    /** @var Run */
    protected $run;

    /**
     * WhoopsRenderer constructor.
     *
     * @param Run $run
     */
    public function __construct(Run $run, bool $inDevelopmentEnvironment = false)
    {
        $this->run = $run;

        parent::__construct($inDevelopmentEnvironment);
    }

    /**
     * @return Run
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * Renders an exception
     *
     * @param Throwable|Exception $ex The thrown exception
     */
    public function render($ex)
    {
        $this->run->handleException($ex);
    }
}
