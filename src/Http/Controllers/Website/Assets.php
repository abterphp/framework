<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Website;

use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

class Assets extends ControllerAbstract
{
    /**
     * 404 page
     *
     * @return Response The response
     */
    public function asset(): Response
    {
        $this->view = $this->viewFactory->createView('contents/frontend/501');

        $response = $this->createResponse('501 Not Implemented');
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_IMPLEMENTED);

        return $response;
    }
}
