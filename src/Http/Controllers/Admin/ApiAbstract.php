<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Admin;

use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Domain\Entities\IToJsoner;
use AbterPhp\Framework\Http\Service\Execute\RepoServiceAbstract;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Orm\OrmException;
use Opulence\Routing\Controller;
use Psr\Log\LoggerInterface;

abstract class ApiAbstract extends Controller
{
    const LOG_MSG_CREATE_FAILURE = 'Creating %1$s failed.';
    const LOG_MSG_UPDATE_FAILURE = 'Updating %1$s with id "%2$s" failed.';
    const LOG_MSG_DELETE_FAILURE = 'Deleting %1$s with id "%2$s" failed.';
    const LOG_MSG_GET_FAILURE    = 'Retrieving %1$s with id "%2$s" failed.';
    const LOG_MSG_LIST_FAILURE   = 'Retrieving %1$s failed.';

    const LOG_CONTEXT_EXCEPTION  = 'Exception';
    const LOG_PREVIOUS_EXCEPTION = 'Previous exception #%d';

    const ENTITY_SINGULAR = '';
    const ENTITY_PLURAL   = '';

    /** @var LoggerInterface */
    protected $logger;

    /** @var RepoService */
    protected $repoService;

    /** @var FoundRows */
    protected $foundRows;

    /**
     * ApiAbstract constructor.
     *
     * @param LoggerInterface     $logger
     * @param RepoServiceAbstract $repoService
     * @param FoundRows           $foundRows
     */
    public function __construct(LoggerInterface $logger, RepoServiceAbstract $repoService, FoundRows $foundRows)
    {
        $this->logger      = $logger;
        $this->repoService = $repoService;
        $this->foundRows   = $foundRows;
    }

    /**
     * @param string $entityId
     *
     * @return Response
     */
    public function get(string $entityId): Response
    {
        try {
            $entity = $this->repoService->retrieveEntity($entityId);
        } catch (\Exception $e) {
            $msg = sprintf(static::LOG_MSG_GET_FAILURE, static::ENTITY_SINGULAR);

            return $this->handleException($msg, $e);
        }

        return $this->handleGetSuccess($entity);
    }

    /**
     * @return Response
     */
    public function list(): Response
    {
        $query = $this->request->getQuery();

        $offset = (int)$query->get('offset', 0);
        $limit  = (int)$query->get('limit', 100);

        try {
            $entities = $this->repoService->retrieveList($offset, $limit, [], [], []);
        } catch (\Exception $e) {
            $msg = sprintf(static::LOG_MSG_LIST_FAILURE, static::ENTITY_PLURAL);

            return $this->handleException($msg, $e);
        }

        $maxCount = $this->foundRows->get();

        return $this->handleListSuccess($entities, $maxCount);
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
            $fileData = $this->getFileData($data);
            $entity   = $this->repoService->create($data, $fileData);
        } catch (\Exception $e) {
            $msg = sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR);

            return $this->handleException($msg, $e);
        }

        return $this->handleCreateSuccess($entity);
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
            $fileData = $this->getFileData($data);
            $entity   = $this->repoService->retrieveEntity($entityId);
            $this->repoService->update($entity, $data, $fileData);
        } catch (\Exception $e) {
            if ($this->isEntityNotFound($e)) {
                return $this->handleNotFound();
            }

            $msg = sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId);

            return $this->handleException($msg, $e);
        }

        return $this->handleUpdateSuccess($entity);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param array $data
     *
     * @return UploadedFile[]
     */
    protected function getFileData(array $data): array
    {
        return [];
    }

    /**
     * @param string $entityId
     *
     * @return Response
     */
    public function delete(string $entityId): Response
    {
        try {
            $entity = $this->repoService->retrieveEntity($entityId);
            $this->repoService->delete($entity);
        } catch (\Exception $e) {
            if ($this->isEntityNotFound($e)) {
                return $this->handleNotFound();
            }

            $msg = sprintf(static::LOG_MSG_DELETE_FAILURE, static::ENTITY_SINGULAR, $entityId);

            return $this->handleException($msg, $e);
        }

        return $this->handleDeleteSuccess();
    }

    /**
     * @param \Exception $e
     *
     * @return bool
     */
    protected function isEntityNotFound(\Exception $e): bool
    {
        if (!($e instanceof OrmException)) {
            return false;
        }

        return $e->getMessage() === 'Failed to find entity';
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
     * @param IStringerEntity $entity
     *
     * @return Response
     */
    protected function handleGetSuccess(IStringerEntity $entity): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_OK);
        $response->setContent($entity->toJSON());

        return $response;
    }

    /**
     * @param array $entities
     * @param int   $total
     *
     * @return Response
     */
    protected function handleListSuccess(array $entities, int $total): Response
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->toJSON();
        }
        $content = sprintf('{"total":%d,"data":[%s]}', $total, implode(',', $data));

        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_OK);
        $response->setContent($content);

        return $response;
    }

    /**
     * @param IStringerEntity $entity
     *
     * @return Response
     */
    protected function handleCreateSuccess(IStringerEntity $entity): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_CREATED);
        $response->setContent($entity->toJSON());

        return $response;
    }

    /**
     * @param IStringerEntity $entity
     *
     * @return Response
     */
    protected function handleUpdateSuccess(IStringerEntity $entity): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_OK);
        $response->setContent($entity->toJSON());

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleDeleteSuccess(): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_NO_CONTENT);

        return $response;
    }

    /**
     * @return Response
     */
    protected function handleNotFound(): Response
    {
        $response = new Response();
        $response->setStatusCode(ResponseHeaders::HTTP_NOT_FOUND);

        return $response;
    }
}
