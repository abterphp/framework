<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Http\Middleware\Security;
use Opulence\Cache\ICacheBridge;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

class SecurityBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            Security::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        $cacheBridge = $container->resolve(ICacheBridge::class);
        $environment = Environment::getVar(Env::ENV_NAME);

        $security = new Security($cacheBridge, $environment);

        $container->bindInstance(Security::class, $security);
    }
}
