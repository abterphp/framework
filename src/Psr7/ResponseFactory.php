<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Psr7;

use Nyholm\Psr7\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    /**
     * @return ResponseInterface
     */
    public function create(): ResponseInterface
    {
        return new Response(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
    }
}
