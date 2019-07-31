<?php

namespace AbterPhp\Framework\Debug\Exceptions\Handlers\Whoops;

use Exception;
use Opulence\Framework\Debug\Exceptions\Handlers\Http;
use Throwable;
use Whoops\RunInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class ExceptionRenderer extends Http\ExceptionRenderer implements Http\IExceptionRenderer
{
    /** @var RunInterface */
    protected $run;

    /**
     * WhoopsRenderer constructor.
     *
     * @param RunInterface $run
     */
    public function __construct(RunInterface $run, bool $inDevelopmentEnvironment = false)
    {
        $this->run = $run;

        parent::__construct($inDevelopmentEnvironment);
    }

    /**
     * @return RunInterface
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
