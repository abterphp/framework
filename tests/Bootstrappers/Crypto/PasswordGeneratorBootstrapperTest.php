<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Crypto;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class PasswordGeneratorBootstrapperTest extends TestCase
{
    /** @var PasswordGeneratorBootstrapper - System Under Test */
    protected PasswordGeneratorBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new PasswordGeneratorBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::OAUTH2_SECRET_LENGTH);
    }

    public function testRegisterBindings(): void
    {
        Environment::setVar(Env::OAUTH2_SECRET_LENGTH, '128');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(ComputerPasswordGenerator::class);
        $this->assertInstanceOf(ComputerPasswordGenerator::class, $actual);
    }
}
