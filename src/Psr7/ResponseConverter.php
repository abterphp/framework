<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Psr7;

use Opulence\Http\Responses\Response as OpulenceResponse;
use Psr\Http\Message\ResponseInterface;

class ResponseConverter
{
    /**
     * @param ResponseInterface $psrResponse
     *
     * @return OpulenceResponse
     */
    public function fromPsr(ResponseInterface $psrResponse): OpulenceResponse
    {
        $content     = $psrResponse->getBody();
        $statusCode  = $psrResponse->getStatusCode();
        $headers     = $psrResponse->getHeaders();

        $response = new OpulenceResponse($content, $statusCode, $headers);

        return $response;
    }
}
