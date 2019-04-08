<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers;

use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Controller;

abstract class ControllerAbstract extends Controller
{
    const VAR_TITLE       = 'title';
    const VAR_MSG_ERROR   = 'errorMessages';
    const VAR_MSG_SUCCESS = 'successMessages';

    /** @var array */
    protected $viewVarsExtra = [];

    /** @var FlashService */
    protected $flashService;

    /**
     * @param FlashService $flashService
     */
    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
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
     * @throws \Throwable
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
