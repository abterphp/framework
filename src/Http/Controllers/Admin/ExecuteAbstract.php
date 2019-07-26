<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Admin;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Form\Extra\DefaultButtons;
use AbterPhp\Framework\Http\Service\Execute\IRepoService;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Casbin\Exceptions\CasbinException;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Orm\OrmException;
use Opulence\Routing\Urls\URLException;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

abstract class ExecuteAbstract extends AdminAbstract
{
    use UrlTrait;
    use MessageTrait;

    const INPUT_NEXT = 'next';

    const URL_CREATE = '%s-create';

    const CREATE_SUCCESS = 'framework:create-success';
    const CREATE_FAILURE = 'framework:create-failure';
    const UPDATE_SUCCESS = 'framework:update-success';
    const UPDATE_FAILURE = 'framework:update-failure';
    const DELETE_SUCCESS = 'framework:delete-success';
    const DELETE_FAILURE = 'framework:delete-failure';

    const LOG_MSG_CREATE_FAILURE = 'Creating %1$s failed.';
    const LOG_MSG_CREATE_SUCCESS = 'Creating %1$s was successful.';
    const LOG_MSG_UPDATE_FAILURE = 'Updating %1$s with id "%2$s" failed.';
    const LOG_MSG_UPDATE_SUCCESS = 'Updating %1$s with id "%2$s" was successful.';
    const LOG_MSG_DELETE_FAILURE = 'Deleting %1$s with id "%2$s" failed.';
    const LOG_MSG_DELETE_SUCCESS = 'Deleting %1$s with id "%2$s" was successful.';

    const ENTITY_TITLE_SINGULAR = '';
    const ENTITY_TITLE_PLURAL   = '';

    /** @var IRepoService */
    protected $repoService;

    /** @var ISession */
    protected $session;

    /**
     * ExecuteAbstract constructor.
     *
     * @param FlashService    $flashService
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param LoggerInterface $logger
     * @param IRepoService    $repoService
     * @param ISession        $session
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        LoggerInterface $logger,
        IRepoService $repoService,
        ISession $session
    ) {
        parent::__construct($flashService, $translator, $urlGenerator, $logger);

        $this->repoService = $repoService;
        $this->session     = $session;
    }

    /**
     * @return Response
     * @throws CasbinException
     * @throws OrmException
     * @throws URLException
     * @throws \Throwable
     */
    public function create(): Response
    {
        $postData = $this->getPostData();
        $fileData = $this->getFileData();

        $errors = $this->repoService->validateForm(array_merge($postData, $fileData));

        if (count($errors) > 0) {
            $this->flashService->mergeErrorMessages($errors);
            $this->logger->info(sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR), $errors);

            return $this->redirectToNext();
        }

        try {
            $entity = $this->repoService->create($postData, $fileData);

            $this->logger->info(sprintf(static::LOG_MSG_CREATE_SUCCESS, static::ENTITY_SINGULAR));
            $this->flashService->mergeSuccessMessages([$this->getMessage(static::CREATE_SUCCESS)]);
        } catch (\Exception $e) {
            $this->flashService->mergeErrorMessages([$this->getMessage(static::CREATE_FAILURE)]);
            $this->logger->info(
                sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR),
                $this->getExceptionContext($e)
            );

            return $this->redirectToNext();
        }

        return $this->redirectToNext($entity);
    }

    /**
     * @param string $entityId
     *
     * @return Response
     * @throws CasbinException
     * @throws OrmException
     * @throws URLException
     * @throws \Throwable
     */
    public function update(string $entityId): Response
    {
        $postData = $this->getPostData();
        $fileData = $this->getFileData();

        $errors = $this->repoService->validateForm(array_merge($postData, $fileData));

        try {
            $entity = $this->repoService->retrieveEntity($entityId);
        } catch (OrmException $e) {
            return $this->redirectToNext();
        }

        if (count($errors) > 0) {
            $this->logger->info(sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId), $errors);
            $this->flashService->mergeErrorMessages($errors);

            return $this->redirectToNext($entity);
        }

        try {
            $this->repoService->update($entity, $postData, $fileData);
            $this->logger->info(sprintf(static::LOG_MSG_UPDATE_SUCCESS, static::ENTITY_SINGULAR, $entityId));
            $this->flashService->mergeSuccessMessages([$this->getMessage(static::UPDATE_SUCCESS)]);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId),
                $this->getExceptionContext($e)
            );
            $this->flashService->mergeErrorMessages([$this->getMessage(static::UPDATE_FAILURE)]);
        }

        return $this->redirectToNext($entity);
    }

    /**
     * @param string $entityId
     *
     * @return Response
     * @throws CasbinException
     * @throws OrmException
     * @throws URLException
     * @throws \Throwable
     */
    public function delete(string $entityId): Response
    {
        $entity = $this->repoService->retrieveEntity($entityId);

        try {
            $this->repoService->delete($entity);
            $this->logger->info(sprintf(static::LOG_MSG_DELETE_SUCCESS, static::ENTITY_SINGULAR, $entityId));
            $this->flashService->mergeSuccessMessages([$this->getMessage(static::DELETE_SUCCESS)]);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(static::LOG_MSG_DELETE_FAILURE, static::ENTITY_SINGULAR, $entityId),
                [static::LOG_CONTEXT_EXCEPTION => $e->getMessage()]
            );
            $this->flashService->mergeErrorMessages([$this->getMessage(static::DELETE_FAILURE)]);
        }

        return $this->redirectToNext();
    }

    /**
     * @return array
     */
    protected function getPostData(): array
    {
        $postData = $this->request->getPost()->getAll();

        return $postData;
    }

    /**
     * @return array
     */
    protected function getFileData(): array
    {
        $fileData = $this->request->getFiles()->getAll();

        return $fileData;
    }

    /**
     * @param IStringerEntity|null $entity
     *
     * @return Response
     * @throws URLException
     */
    protected function redirectToNext(?IStringerEntity $entity = null): Response
    {
        $next = $this->request->getInput(static::INPUT_NEXT, DefaultButtons::BTN_VALUE_NEXT_BACK);

        $entityId = $entity ? $entity->getId() : null;

        $url = $this->getUrl($next, $entityId);

        $response = new RedirectResponse($url);
        $response->send();

        return $response;
    }

    /**
     * @param string        $next
     * @param string|null $entityId
     *
     * @return string
     * @throws URLException
     */
    protected function getUrl(string $next, string $entityId = null)
    {
        switch ($next) {
            case DefaultButtons::BTN_VALUE_NEXT_BACK:
                return $this->getShowUrl();
            case DefaultButtons::BTN_VALUE_NEXT_EDIT:
                if (null === $entityId) {
                    return $this->getCreateUrl();
                }
                return $this->getEditUrl($entityId);
            case DefaultButtons::BTN_VALUE_NEXT_CREATE:
                return $this->getCreateUrl();
        }

        return $this->getCreateUrl();
    }

    /**
     * @return string
     * @throws URLException
     */
    protected function getCreateUrl(): string
    {
        $urlName = strtolower(sprintf(static::URL_CREATE, static::ENTITY_PLURAL));
        $url     = $this->urlGenerator->createFromName($urlName);

        return $url;
    }
}
