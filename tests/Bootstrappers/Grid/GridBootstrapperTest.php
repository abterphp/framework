<?php

namespace AbterPhp\Framework\Bootstrappers\Grid;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Grid\Pagination\Options as PaginationOptions;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Ioc\Container;
use PHPUnit\Framework\TestCase;

class GridBootstrapperTest extends TestCase
{
    /** @var GridBootstrapper - System Under Test */
    protected GridBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new GridBootstrapper();
    }

    protected function tearDown(): void
    {
        Environment::unsetVar(Env::PAGINATION_SIZE_OPTIONS);
        Environment::unsetVar(Env::ENV_NAME);
    }

    public function testRegisterBindings()
    {
        Environment::setVar(Env::PAGINATION_SIZE_OPTIONS, '5,25');
        Environment::setVar(Env::ENV_NAME, 'foo');

        $mockTranslator = $this->getMockBuilder(ITranslator::class)->getMock();

        $container = new Container();
        $container->bindInstance(ITranslator::class, $mockTranslator);

        $this->sut->registerBindings($container);

        $actual = $container->resolve(PaginationOptions::class);
        $this->assertInstanceOf(PaginationOptions::class, $actual);
    }
}
