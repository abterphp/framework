<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Admin;

use AbterPhp\Framework\Http\Service\Execute\RepoServiceAbstract;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Controller;
use Psr\Log\LoggerInterface;

abstract class ApiAbstract extends Controller
{
    const LOG_MSG_CREATE_FAILURE = 'Creating %1$s failed.';
    const LOG_MSG_CREATE_SUCCESS = 'Creating %1$s was successful.';
    const LOG_MSG_UPDATE_FAILURE = 'Updating %1$s with id "%2$s" failed.';
    const LOG_MSG_UPDATE_SUCCESS = 'Updating %1$s with id "%2$s" was successful.';
    const LOG_MSG_DELETE_FAILURE = 'Deleting %1$s with id "%2$s" failed.';
    const LOG_MSG_DELETE_SUCCESS = 'Deleting %1$s with id "%2$s" was successful.';

    const LOG_CONTEXT_EXCEPTION  = 'Exception';
    const LOG_PREVIOUS_EXCEPTION = 'Previous exception #%d';

    const ENTITY_TITLE_SINGULAR = '';
    const ENTITY_TITLE_PLURAL   = '';

    /** @var LoggerInterface */
    protected $logger;

    /** @var RepoService */
    protected $repoService;

    /**
     * ApiAbstract constructor.
     *
     * @param LoggerInterface     $logger
     * @param RepoServiceAbstract $repoService
     */
    public function __construct(LoggerInterface $logger, RepoServiceAbstract $repoService)
    {
        $this->logger      = $logger;
        $this->repoService = $repoService;
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $data = $this->getCreateData();

        $errors = $this->repoService->validateForm($data);

        if (count($errors) > 0) {
            $msg = sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR);

            return $this->handleErrors($msg, $errors);
        }

        try {
            $entityId = $this->repoService->create($data, []);
        } catch (\Exception $e) {
            $msg = sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR);

            return $this->handleException($msg, $e);
        }

        return $this->handleCreateSuccess($entityId);
    }

    /**
     * @param string $entityId
     *
     * @return Response
     */
    public function update(string $entityId): Response
    {
        $data = $this->getUpdateData();

        $errors = $this->repoService->validateForm($data);

        if (count($errors) > 0) {
            $msg = sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId);

            return $this->handleErrors($msg, $errors);
        }

        try {
            $this->repoService->update($entityId, $data, []);
        } catch (\Exception $e) {
            $msg = sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId);

            return $this->handleException($msg, $e);
        }

        return $this->handleUpdateSuccess($entityId);
    }

    /**
     * @param string $entityId
     *
     * @return Response
     */
    public function delete(string $entityId): Response
    {
        try {
            $entityId = $this->repoService->delete($entityId);
        } catch (\Exception $e) {
            $msg = sprintf(static::LOG_MSG_DELETE_FAILURE, static::ENTITY_SINGULAR, $entityId);

            return $this->handleException($msg, $e);
        }

        return $this->handleDeleteSuccess($entityId);
    }

    /**
     * @return array
     */
    public function getCreateData(): array
    {
        return $this->getSharedData();
    }

    /**
     * @return array
     */
    public function getUpdateData(): array
    {
        return $this->getSharedData();
    }

    /**
     * @return array
     */
    public function getSharedData(): array
    {
        return $this->request->getJsonBody();
    }

    /**
     * @param string $msg
     * @param array  $errors
     *
     * @return Response
     */
    protected function handleErrors(string $msg, array $errors): Response
    {
        $this->logger->debug($msg);

        $response = new Response();

        $response->setStatusCode(ResponseHeaders::HTTP_BAD_REQUEST);

        $response->setContent(json_encode(['errors' => $errors]));

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

        $response = new Response();

        $response->setStatusCode(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);

        $response->setContent(json_encode(['errors' => [$msg]]));

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

    /**
     * @return Response
     */
    protected function handleCreateSuccess(string $entityId): Response
    {
        $response = new Response();

        $response->setStatusCode(ResponseHeaders::HTTP_CREATED);

        $response->setContent(json_encode(['id' => $entityId]));

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleUpdateSuccess(string $entityId): Response
    {
        $response = new Response();

        $response->setStatusCode(ResponseHeaders::HTTP_OK);

        $response->setContent(json_encode(['id' => $entityId]));

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleDeleteSuccess(string $entityId): Response
    {
        $response = new Response();

        $response->setStatusCode(ResponseHeaders::HTTP_OK);

        $response->setContent(json_encode(['id' => $entityId]));

        return $response;
    }
}
