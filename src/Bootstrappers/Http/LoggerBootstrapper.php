<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Psr\Log\LoggerInterface;

class LoggerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            LoggerInterface::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $logger   = new Logger('application');
        $filePath = Environment::mustGetVar(Env::DIR_LOGS);
        $logger->pushHandler(new StreamHandler($filePath . '/application.log', Level::Info));

        $container->bindInstance(LoggerInterface::class, $logger);
    }
}
