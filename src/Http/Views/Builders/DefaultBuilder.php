<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Views\Builders;

use AbterPhp\Framework\Constant\View;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Views\Factories\IViewBuilder;
use Opulence\Views\IView;

/**
 * Defines the master view builder
 */
class DefaultBuilder implements IViewBuilder
{
    /** @var IEventDispatcher */
    protected $eventDispatcher;

    /**
     * WebsiteBuilder constructor.
     *
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(IEventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function build(IView $view): IView
    {
        $view->setVar('title', '');
        $view->setVar('metaKeywords', []);
        $view->setVar('metaDescription', '');
        $view->setVar('metaAuthor', '');
        $view->setVar('metaCopyright', '');
        $view->setVar('metaRobots', '');
        $view->setVar('metaOGImage', '');
        $view->setVar('metaOGDescription', '');
        $view->setVar('metaOGTitle', '');
        $view->setVar('siteTitle', '');
        $view->setVar('homepageUrl', '');
        $view->setVar('pageUrl', '');
        $view->setVar('layout', '');
        $view->setVar('page', '');

        $view->setVar(View::PRE_HEADER, '');
        $view->setVar(View::HEADER, '');
        $view->setVar(View::POST_HEADER, '');

        $view->setVar(View::PRE_FOOTER, '');
        $view->setVar(View::FOOTER, '');
        $view->setVar(View::POST_FOOTER, '');

        return $view;
    }
}
