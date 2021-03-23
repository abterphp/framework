<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Vendor;

use AbterPhp\Framework\Assets\UrlFixer;
use AbterPhp\Framework\Config\Routes;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

class UrlFixerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            UrlFixer::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        /** @var Routes $routes */
        $routes = $container->resolve(Routes::class);

        $urlFixer = new UrlFixer($routes->getCacheUrl());

        $container->bindInstance(UrlFixer::class, $urlFixer);
    }
}
