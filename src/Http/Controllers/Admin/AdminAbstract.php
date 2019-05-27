<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Controllers\Admin;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

abstract class AdminAbstract extends ControllerAbstract
{
    const ENTITY_PLURAL   = '';
    const ENTITY_SINGULAR = '';

    const ENTITY_LOAD_FAILURE = 'framework:load-failure';

    const URL_EDIT = '%s-edit';

    const RESOURCE_DEFAULT = '%s';
    const RESOURCE_HEADER  = '%s-header';
    const RESOURCE_FOOTER  = '%s-footer';
    const RESOURCE_TYPE    = 'void';

    const LOG_CONTEXT_EXCEPTION  = 'Exception';
    const LOG_PREVIOUS_EXCEPTION = 'Previous exception #%d';

    /** @var ITranslator */
    protected $translator;

    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $resource = '';

    /**
     * AdminAbstract constructor.
     *
     * @param FlashService    $flashService
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param LoggerInterface $logger
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        LoggerInterface $logger
    ) {
        parent::__construct($flashService);

        $this->translator   = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->logger       = $logger;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param IStringerEntity|null $entity
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        $this->prepareCustomAssets();
    }

    protected function prepareCustomAssets()
    {
        $this->view->setVar('page', $this->getResourceName(static::RESOURCE_DEFAULT));
        $this->view->setVar('pageHeader', $this->getResourceName(static::RESOURCE_HEADER));
        $this->view->setVar('pageFooter', $this->getResourceName(static::RESOURCE_FOOTER));
        $this->view->setVar('pageType', $this->getResourceTypeName(static::RESOURCE_DEFAULT));
        $this->view->setVar('pageTypeHeader', $this->getResourceTypeName(static::RESOURCE_HEADER));
        $this->view->setVar('pageTypeFooter', $this->getResourceTypeName(static::RESOURCE_FOOTER));
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function getResourceName(string $template)
    {
        return sprintf($template, static::ENTITY_SINGULAR);
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function getResourceTypeName(string $template)
    {
        return sprintf($template, static::RESOURCE_TYPE);
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
}
