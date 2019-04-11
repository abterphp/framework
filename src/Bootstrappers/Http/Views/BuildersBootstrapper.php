<?php

namespace AbterPhp\Framework\Bootstrappers\Http\Views;

use AbterPhp\Framework\Http\Views\Builders\DefaultBuilder;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\IView;

/**
 * Defines the view builders bootstrapper
 */
class BuildersBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        /** @var IViewFactory $viewFactory */
        $viewFactory = $container->resolve(IViewFactory::class);

        $viewFactory->registerBuilder(
            'layouts/default',
            function (IView $view) {
                /** @see DefaultBuilder::build() */
                return (new DefaultBuilder())->build($view);
            }
        );
    }
}
