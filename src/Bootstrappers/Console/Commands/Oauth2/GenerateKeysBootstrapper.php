<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Console\Commands\Oauth2;

use AbterPhp\Framework\Console\Commands\Oauth2\GenerateKeys;
use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class GenerateKeysBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [GenerateKeys::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $privateKeyPassword = Environment::getVar(Env::OAUTH2_PRIVATE_KEY_PASSWORD);
        $privateKeyPath     = Environment::getVar(Env::OAUTH2_PRIVATE_KEY_PATH);
        $publicKeyPath      = Environment::getVar(Env::OAUTH2_PUBLIC_KEY_PATH);

        $command = new GenerateKeys($privateKeyPassword, $privateKeyPath, $publicKeyPath);

        $container->bindInstance(GenerateKeys::class, $command);
    }
}
