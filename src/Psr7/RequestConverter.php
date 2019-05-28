<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Psr7;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Opulence\Http\Requests\Request as OpulenceRequest;
use Psr\Http\Message\RequestInterface;

class RequestConverter
{
    /**
     * @param OpulenceRequest $opulenceRequest
     *
     * @return RequestInterface
     */
    public function toPsr(OpulenceRequest $opulenceRequest): RequestInterface
    {
        $psr17Factory = new Psr17Factory();

        $creator = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );

        $request = $creator->fromGlobals();

        if ($opulenceRequest->isJson() && $opulenceRequest->getRawBody()) {
            $request = $request->withParsedBody($opulenceRequest->getJsonBody());
        }

        return $request;
    }
}
