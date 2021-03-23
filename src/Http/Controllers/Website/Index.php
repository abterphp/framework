<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Website;

use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Throwable;

class Index extends ControllerAbstract
{
    /**
     * 404 page
     *
     * @return Response The response
     * @throws Throwable
     */
    public function asset(): Response
    {
        $this->view = $this->viewFactory->createView('contents/frontend/404');

        $response = $this->createResponse('404 Page not Found');
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_FOUND);

        return $response;
    }

    /**
     * 404 page
     *
     * @return Response The response
     * @throws Throwable
     */
    public function notFound(): Response
    {
        $this->view = $this->viewFactory->createView('contents/frontend/404');

        $response = $this->createResponse('404 Page not Found');
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_FOUND);

        return $response;
    }
}
