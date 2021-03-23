<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Website;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use AbterPhp\Framework\Session\FlashService;
use Dflydev\ApacheMimeTypes\FlatRepository;
use League\Flysystem\FilesystemException;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Psr\Log\LoggerInterface;
use Throwable;

class Assets extends ControllerAbstract
{
    protected const HEADER_CONTENT_TYPE = 'Content-Type';

    /** @var AssetManager */
    protected AssetManager $assetManager;

    /** @var FlatRepository */
    protected FlatRepository $memeRepository;

    /**
     * Assets constructor.
     *
     * @param FlashService    $flashService
     * @param LoggerInterface $logger
     * @param AssetManager    $assetManager
     * @param FlatRepository  $memeRepository
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        AssetManager $assetManager,
        FlatRepository $memeRepository
    ) {
        parent::__construct($flashService, $logger);

        $this->assetManager   = $assetManager;
        $this->memeRepository = $memeRepository;
    }

    /**
     * 404 page
     *
     * @param string $path
     *
     * @return Response The response
     * @throws FilesystemException|Throwable
     */
    public function asset(string $path): Response
    {
        $content = $this->assetManager->renderRaw($path);

        if (null === $content) {
            return $this->createNotFound();
        }

        $ext = substr($path, strrpos($path, '.') + 1);
        if (!$this->isExtAllowed($ext)) {
            return $this->createForbidden();
        }

        $contentType = $this->getContentType($ext);

        $response = new Response();
        $response->setContent($content);
        $response->getHeaders()->add(static::HEADER_CONTENT_TYPE, $contentType);

        return $response;
    }

    /**
     * @param string $ext
     *
     * @return bool
     */
    protected function isExtAllowed(string $ext): bool
    {
        switch ($ext) {
            case 'php':
            case 'php7':
            case 'gitignore':
            case 'phtml':
                return false;
        }

        return true;
    }

    /**
     * @param string $ext
     *
     * @return string
     */
    protected function getContentType(string $ext): string
    {
        $type = $this->memeRepository->findType($ext);
        if ($type) {
            return $type;
        }

        return 'text/plain';
    }

    /**
     * @return Response
     * @throws Throwable
     */
    protected function createNotFound(): Response
    {
        $this->view = $this->viewFactory->createView('contents/frontend/404');

        $response = $this->createResponse('404 Not Found');
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_FOUND);

        return $response;
    }

    /**
     * @return Response
     * @throws Throwable
     */
    protected function createForbidden(): Response
    {
        $this->view = $this->viewFactory->createView('contents/frontend/403');

        $response = $this->createResponse('403 Forbidden');
        $response->setStatusCode(ResponseHeaders::HTTP_FORBIDDEN);

        return $response;
    }
}
