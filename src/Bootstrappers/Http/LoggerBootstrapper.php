<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Http;

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
            LoggerInterface::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $logger   = new Logger('application');
        $filePath = getenv(\AbterPhp\Framework\Constant\Env::DIR_LOGS);
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($filePath . '/application.log', Logger::INFO));

        $container->bindInstance(LoggerInterface::class, $logger);
    }
}
