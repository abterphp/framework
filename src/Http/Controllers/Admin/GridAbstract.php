<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Admin;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Events\GridReady;
use AbterPhp\Framework\Grid\Factory\IBase as GridFactory;
use AbterPhp\Framework\Grid\Pagination\Options as PaginationOptions;
use AbterPhp\Framework\Http\Service\RepoGrid\IRepoGrid;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Orm\IGridRepo;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

abstract class GridAbstract extends AdminAbstract
{
    const ENTITY_PLURAL       = '';
    const ENTITY_TITLE_PLURAL = '';

    const VIEW_LIST = 'contents/backend/grid';

    const VAR_GRID       = 'grid';
    const VAR_CREATE_URL = 'createUrl';

    const TITLE_SHOW = 'framework:titleList';

    const URL_CREATE = '%s-create';

    const RESOURCE_DEFAULT = '%s-grid';
    const RESOURCE_HEADER  = '%s-header-grid';
    const RESOURCE_FOOTER  = '%s-footer-grid';
    const RESOURCE_TYPE    = 'grid';

    /** @var IGridRepo */
    protected $gridRepo;

    /** @var FoundRows */
    protected $foundRows;

    /** @var GridFactory */
    protected $gridFactory;

    /** @var PaginationOptions */
    protected $paginationOptions;

    /** @var AssetManager */
    protected $assets;

    /** @var IRepoGrid */
    protected $repoGrid;

    /** @var IEventDispatcher */
    protected $eventDispatcher;

    /**
     * GridAbstract constructor.
     *
     * @param FlashService     $flashService
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param LoggerInterface  $logger
     * @param AssetManager     $assets
     * @param IRepoGrid        $repoGrid
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        LoggerInterface $logger,
        AssetManager $assets,
        IRepoGrid $repoGrid,
        IEventDispatcher $eventDispatcher
    ) {
        parent::__construct($flashService, $translator, $urlGenerator, $logger);

        $this->assets          = $assets;
        $this->repoGrid        = $repoGrid;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return Response
     * @throws \Casbin\Exceptions\CasbinException
     * @throws \Throwable
     */
    public function show(): Response
    {
        $grid = $this->repoGrid->createAndPopulate($this->request->getQuery(), $this->getBaseUrl());

        $this->eventDispatcher->dispatch(Event::GRID_READY, new GridReady($grid));

        $grid->setTranslator($this->translator);

        $title = $this->translator->translate(static::TITLE_SHOW, static::ENTITY_TITLE_PLURAL);

        $this->view = $this->viewFactory->createView(static::VIEW_LIST);
        $this->view->setVar(static::VAR_GRID, $grid);
        $this->view->setVar(static::VAR_CREATE_URL, $this->getCreateUrl());

        $this->addCustomAssets();

        return $this->createResponse($title);
    }

    /**
     * @param IStringerEntity|null $entity
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        $this->prepareCustomAssets();

        $this->addTypeAssets();
    }

    protected function addTypeAssets()
    {
        $groupName = $this->getResourceTypeName(static::RESOURCE_FOOTER);

        $this->assets->addJs($groupName, '/admin-assets/js/hideable-container.js');
        $this->assets->addJs($groupName, '/admin-assets/js/filters.js');
        $this->assets->addJs($groupName, '/admin-assets/js/tooltips.js');
        $this->assets->addJs($groupName, '/admin-assets/js/pagination.js');
    }

    /**
     * @return string
     * @throws \Opulence\Routing\Urls\URLException
     */
    protected function getBaseUrl(): string
    {
        return $this->urlGenerator->createFromName(static::ENTITY_PLURAL) . '?';
    }

    /**
     * @return string
     * @throws \Opulence\Routing\Urls\URLException
     */
    protected function getCreateUrl(): string
    {
        $urlName = strtolower(sprintf(static::URL_CREATE, static::ENTITY_PLURAL));
        $url     = $this->urlGenerator->createFromName($urlName);

        return $url;
    }
}
