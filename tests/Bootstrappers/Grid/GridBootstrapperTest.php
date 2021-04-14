<?php

declare(strict_types=1);

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
        Environment::unsetVar(Env::PAGINATION_SIZE_OPTIONS);
        Environment::unsetVar(Env::ENV_NAME);

        $this->sut = new GridBootstrapper();
    }

    public function testRegisterBindings(): void
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
