<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers;

use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Controller;
use Psr\Log\LoggerInterface;
use Throwable;

abstract class ControllerAbstract extends Controller
{
    protected const VAR_TITLE       = 'title';
    protected const VAR_MSG_ERROR   = 'errorMessages';
    protected const VAR_MSG_SUCCESS = 'successMessages';

    /** @var array<string,mixed> */
    protected array $viewVarsExtra = [];

    protected FlashService $flashService;

    protected LoggerInterface $logger;

    /**
     * @param FlashService    $flashService
     * @param LoggerInterface $logger
     */
    public function __construct(FlashService $flashService, LoggerInterface $logger)
    {
        $this->flashService = $flashService;
        $this->logger       = $logger;
    }

    /**
     * @param string $title
     */
    protected function sharedViewSetup(string $title)
    {
        $this->view->setVar(static::VAR_TITLE, $title);
        $this->view->setVar(static::VAR_MSG_ERROR, $this->flashService->retrieveErrorMessages());
        $this->view->setVar(static::VAR_MSG_SUCCESS, $this->flashService->retrieveSuccessMessages());
    }

    /**
     * @param string $title
     *
     * @return Response
     * @throws Throwable
     */
    protected function createResponse(string $title = ''): Response
    {
        $this->sharedViewSetup($title);

        foreach ($this->viewVarsExtra as $viewKey => $viewValue) {
            $this->view->setVar($viewKey, $viewValue);
        }

        return new Response($this->viewCompiler->compile($this->view));
    }
}
