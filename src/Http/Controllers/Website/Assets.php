<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Website;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

class Assets extends ControllerAbstract
{
    /** @var AssetManager */
    protected $assetManager;

    /**
     * Assets constructor.
     *
     * @param FlashService $flashService
     * @param AssetManager $cacheManager
     */
    public function __construct(FlashService $flashService, AssetManager $assetManager)
    {
        parent::__construct($flashService);

        $this->assetManager = $assetManager;
    }

    /**
     * 404 page
     *
     * @param string $path
     *
     * @return Response The response
     */
    public function asset(string $path): Response
    {
        $content = $this->assetManager->renderRaw($path);

        if (null === $content) {
            return $this->create404();
        }

        $contentType = $this->getContentType($path);

        $response = new Response();
        $response->setContent($content);
        $response->getHeaders()->add('Content-Type', $contentType);

        return $response;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getContentType(string $path): string
    {
        $ext = substr($path, strrpos($path, '.'));
        switch ($ext) {
            case '.css':
                return 'text/css';
            case '.js':
                return 'text/css';
            case '.htm':
            case '.html':
                return 'text/html';
            case '.jpg':
            case '.jpeg':
                return 'image/jpeg';
            case '.png':
                return 'image/png';
            case '.gif':
                return 'image/gif';
            case '.svg':
                return 'image/svg+xml';
            case '.ico':
                return 'image/x-icon';
        }

        return 'text/plain';
    }

    protected function create404(): Response
    {
        $this->view = $this->viewFactory->createView('contents/frontend/404');

        $response = $this->createResponse('404 Not Found');
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_FOUND);

        return $response;
    }
}
