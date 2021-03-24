<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Views;

use Opulence\Http\Requests\Request;
use Opulence\Ioc\Container;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;
use Opulence\Views\Compilers\Fortune\Transpiler;
use PHPUnit\Framework\TestCase;

class ViewFunctionsBootstrapperTest extends TestCase
{
    /** @var ViewFunctionsBootstrapper - System Under Test */
    protected ViewFunctionsBootstrapper $sut;

    public function setUp(): void
    {
        $this->sut = new ViewFunctionsBootstrapper();
    }

    public function testRegisterBindings(): void
    {
        $requestMock      = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $urlGeneratorMock = $this->getMockBuilder(UrlGenerator::class)->disableOriginalConstructor()->getMock();
        $sessionMock      = $this->getMockBuilder(ISession::class)->getMock();
        $transpilerMock   = $this->getMockBuilder(Transpiler::class)->disableOriginalConstructor()->getMock();

        $transpilerMock->expects($this->atLeast(3))->method('registerViewFunction');

        $container = new Container();
        $container->bindInstance(Request::class, $requestMock);
        $container->bindInstance(UrlGenerator::class, $urlGeneratorMock);
        $container->bindInstance(ISession::class, $sessionMock);
        $container->bindInstance(ITranspiler::class, $transpilerMock);

        $this->sut->registerBindings($container);
    }

    public function testCreateMetaViewFunction(): void
    {
        $nameStub = 'foo';
        $contentStubs = ['', 'bar', 'baz'];

        $actual = $this->sut->createMetaViewFunction()($nameStub, ...$contentStubs);
        $this->assertSame("<meta property=\"$nameStub\" name=\"$nameStub\" content=\"{$contentStubs[1]}\">\n", $actual);
    }

    public function testCreateAuthorNameViewFunction(): void
    {
        $authorStub = 'foo';

        $actual = $this->sut->createAuthorNameViewFunction()($authorStub);
        $this->assertSame("<meta property=\"author\" name=\"author\" content=\"{$authorStub}\">\n", $actual);
    }

    public function testCreateAuthorLinkViewFunction(): void
    {
        $authorStub = 'https://foo.example.com/';

        $actual = $this->sut->createAuthorLinkViewFunction()($authorStub);
        $this->assertSame("<link rel=\"author\" href=\"{$authorStub}\">\n", $actual);
    }
}
