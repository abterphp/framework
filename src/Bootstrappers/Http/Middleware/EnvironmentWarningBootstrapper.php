<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Http\Middleware\EnvironmentWarning;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class EnvironmentWarningBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            EnvironmentWarning::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        /** @var ITranslator $translator */
        $translator  = $container->resolve(ITranslator::class);
        $environment = Environment::getVar(Env::ENV_NAME);

        $environmentWarning = new EnvironmentWarning($translator, $environment);

        $container->bindInstance(EnvironmentWarning::class, $environmentWarning);
    }
}
