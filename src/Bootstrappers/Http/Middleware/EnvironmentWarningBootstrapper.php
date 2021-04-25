<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Http\Middleware\EnvironmentWarning;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

class EnvironmentWarningBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
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
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        $environment = Environment::mustGetVar(Env::ENV_NAME);

        /** @var ITranslator $translator */
        $translator = $container->resolve(ITranslator::class);

        $environmentWarning = new EnvironmentWarning($translator, $environment);

        $container->bindInstance(EnvironmentWarning::class, $environmentWarning);
    }
}
