<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Crypto;

use AbterPhp\Framework\Constant\Env;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class PasswordGeneratorBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            ComputerPasswordGenerator::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        $secretLength = $this->getSecretLength();
        $generator    = new ComputerPasswordGenerator();

        $generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LENGTH, $secretLength);

        $container->bindInstance(ComputerPasswordGenerator::class, $generator);
    }

    /**
     * @return int
     */
    private function getSecretLength(): int
    {
        return (int)getenv(Env::OAUTH2_SECRET_LENGTH);
    }
}
