<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers;

use AbterPhp\Framework\Config\EnvReader;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Http\Service\Execute\RepoServiceAbstract;
use Opulence\Http\Responses\Response;
use Opulence\Orm\OrmException;
use Opulence\Routing\Controller;
use Psr\Log\LoggerInterface;

abstract class ApiAbstract extends Controller
{
    use ApiResponseTrait;
    use ApiIssueTrait;
    use ApiDataTrait;

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
     * @param EnvReader           $envReader
     */
    public function __construct(
        LoggerInterface $logger,
        RepoServiceAbstract $repoService,
        FoundRows $foundRows,
        EnvReader $envReader
    ) {
        $this->logger         = $logger;
        $this->repoService    = $repoService;
        $this->foundRows      = $foundRows;
        $this->problemBaseUrl = $envReader->get(Env::API_PROBLEM_BASE_URL);
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
            $msg = sprintf(static::LOG_MSG_GET_FAILURE, static::ENTITY_SINGULAR, $entityId);

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
        try {
            $data = $this->getCreateData();

            $errors = $this->repoService->validateForm($data);

            if (count($errors) > 0) {
                $msg = sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR);

                return $this->handleErrors($msg, $errors);
            }

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
        try {
            $data = $this->getUpdateData();

            $errors = $this->repoService->validateForm($data);

            if (count($errors) > 0) {
                $msg = sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId);

                return $this->handleErrors($msg, $errors);
            }

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
     * @return string
     */
    protected function getUserIdentifier(): string
    {
        return $this->request->getHeaders()->get('xxx-user-username');
    }
}
