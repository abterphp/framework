<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers;

use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

trait ApiIssueTrait
{
    /** @var string */
    protected $problemBaseUrl;

    /**
     * @param string $msg
     * @param array  $errors
     *
     * @return Response
     */
    protected function handleErrors(string $msg, array $errors): Response
    {
        $this->logger->debug($msg);

        $detail = [];
        foreach ($errors as $key => $keyErrors) {
            foreach ($keyErrors as $keyError) {
                $detail[] = sprintf('%s: %s', $key, $keyError);
            }
        }

        $status  = ResponseHeaders::HTTP_BAD_REQUEST;
        $content = [
            'type'   => sprintf('%sbad-request', $this->problemBaseUrl),
            'title'  => 'Bad Request',
            'status' => $status,
            'detail' => implode("\n", $detail),
        ];

        $response = new Response();
        $response->setStatusCode($status);
        $response->setContent(json_encode($content));

        return $response;
    }

    /**
     * @param string     $msg
     * @param \Exception $exception
     *
     * @return Response
     */
    protected function handleException(string $msg, \Exception $exception): Response
    {
        $this->logger->error($msg, $this->getExceptionContext($exception));

        $status  = ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR;
        $content = [
            'type'   => sprintf('%sinternal-server-error', $this->problemBaseUrl),
            'title'  => 'Internal Server Error',
            'status' => $status,
            'detail' => $exception->getMessage(),
        ];

        $response = new Response();
        $response->setStatusCode($status);
        $response->setContent(json_encode($content));

        return $response;
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    protected function getExceptionContext(\Exception $exception): array
    {
        $result = [static::LOG_CONTEXT_EXCEPTION => $exception->getMessage()];

        $i = 1;
        while ($exception = $exception->getPrevious()) {
            $result[sprintf(static::LOG_PREVIOUS_EXCEPTION, $i++)] = $exception->getMessage();
        }

        return $result;
    }
}
