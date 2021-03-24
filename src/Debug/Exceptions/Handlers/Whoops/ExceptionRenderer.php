<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Debug\Exceptions\Handlers\Whoops;

use Exception;
use Opulence\Framework\Debug\Exceptions\Handlers\Http;
use Opulence\Http\HttpException;
use Throwable;
use Whoops\RunInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class ExceptionRenderer extends Http\ExceptionRenderer implements Http\IExceptionRenderer
{
    /** @var RunInterface */
    protected RunInterface $run;

    public const HTML = <<< EOF
        <!DOCTYPE html>
        <html>
            <head>
                <meta name="viewport" content="initial-scale=1"/>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
                    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
                    crossorigin="anonymous">
            </head>
            <body>
                <main class="main">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm">
                                <div class="alert alert-danger" role="alert">
                                    <h4 class="alert-heading">Exception: %s</h4>
                                    <p>%s</p>
                                    <hr>
                                    <p class="mb-0">
                                        <a href="#" class="btn btn-danger btn-lg active" role="button" aria-pressed="true"
                                        onclick="location.reload(); return false;">Try again</a>
                                        <a href="#" class="btn btn-primary btn-lg active" role="button" aria-pressed="true"
                                        onclick="window.history.back(); return false;">Back to previous page</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </body>
        </html>
        EOF;


    /**
     * @return bool
     */
    public function isInDevelopmentEnvironment(): bool
    {
        return $this->inDevelopmentEnvironment;
    }

    /**
     * @param bool $inDevelopmentEnvironment
     *
     * @return $this
     */
    public function setInDevelopmentEnvironment(bool $inDevelopmentEnvironment): self
    {
        $this->inDevelopmentEnvironment = $inDevelopmentEnvironment;

        return $this;
    }

    /**
     * WhoopsRenderer constructor.
     *
     * @param RunInterface $run
     * @param bool         $inDevelopmentEnvironment
     */
    public function __construct(RunInterface $run, bool $inDevelopmentEnvironment = false)
    {
        $this->run = $run;

        parent::__construct($inDevelopmentEnvironment);
    }

    /**
     * @return RunInterface
     */
    public function getRun(): RunInterface
    {
        return $this->run;
    }

    /**
     * Renders an exception
     *
     * @param Throwable|Exception $ex The thrown exception
     */
    public function render($ex): void
    {
        if (!$this->inDevelopmentEnvironment) {
            $this->run->writeToOutput(false);

            $this->run->unregister();

            $this->devRender($ex);

            return;
        }

        $this->run->handleException($ex);
    }

    /**
     * @param Exception $ex
     */
    public function devRender(Exception $ex): void
    {
        // Add support for HTTP library without having to necessarily depend on it
        $statusCode = 500;
        $headers    = [];
        if ($ex instanceof HttpException) {
            $statusCode = $ex->getStatusCode();
            $headers    = $ex->getHeaders();
        }

        // Always get the content, even if headers are sent, so that we can unit test this
        $content = $ex->getMessage();

        if (headers_sent()) {
            return;
        }

        switch ($this->getRequestFormat()) {
            case 'json':
                $headers['Content-Type'] = 'application/json';
                break;
            default:
                $content                 = sprintf(
                    self::HTML,
                    get_class($ex),
                    $ex->getMessage()
                );
                $headers['Content-Type'] = 'text/html';
        }

        $this->devRenderHeaders($headers, $statusCode);

        echo $content;
        // To prevent any potential output buffering, let's flush
        flush();
    }

    /**
     * @param array $headers
     * @param int   $statusCode
     */
    protected function devRenderHeaders(array $headers, int $statusCode): void
    {
        header("HTTP/1.1 $statusCode", true, $statusCode);

        foreach ($headers as $name => $values) {
            $values = (array)$values;

            foreach ($values as $value) {
                header("$name:$value", false);
            }
        }
    }
}
