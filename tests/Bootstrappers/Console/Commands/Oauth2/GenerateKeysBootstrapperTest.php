<?php

namespace AbterPhp\Framework\Bootstrappers\Console\Commands\Oauth2;

use AbterPhp\Framework\Console\Commands\Oauth2\GenerateKeys;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class GenerateKeysBootstrapperTest extends TestCase
{
    /** @var GenerateKeysBootstrapper - System Under Test */
    protected GenerateKeysBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new GenerateKeysBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::OAUTH2_PRIVATE_KEY_PASSWORD);
        Environment::unsetVar(Env::OAUTH2_PRIVATE_KEY_PATH);
        Environment::unsetVar(Env::OAUTH2_PUBLIC_KEY_PATH);
    }

    public function testRegisterBindings()
    {
        Environment::setVar(Env::OAUTH2_PRIVATE_KEY_PASSWORD, 'foo');
        Environment::setVar(Env::OAUTH2_PRIVATE_KEY_PATH, 'bar');
        Environment::setVar(Env::OAUTH2_PUBLIC_KEY_PATH, 'baz');

        $container = new Container();

        $this->sut->registerBindings($container);

        $actual = $container->resolve(GenerateKeys::class);
        $this->assertInstanceOf(GenerateKeys::class, $actual);
    }
}
