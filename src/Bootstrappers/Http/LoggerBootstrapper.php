<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

use AbterPhp\Framework\Constant\Env;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Opulence\Environments\Environment;
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
            LoggerInterface::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $logger   = new Logger('application');
        $filePath = Environment::getVar(Env::DIR_LOGS);
        $logger->pushHandler(new StreamHandler($filePath . '/application.log', Logger::INFO));

        $container->bindInstance(LoggerInterface::class, $logger);
    }
}
