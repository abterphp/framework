<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Middleware;

use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\I18n\MockTranslatorFactory;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EnvironmentWarningTest extends TestCase
{
    /** @var EnvironmentWarning - System Under Test */
    protected $sut;

    /** @var ITranslator|MockObject */
    protected $translatorMock;

    public function setUp()
    {
        $this->translatorMock = MockTranslatorFactory::createSimpleTranslator($this, []);

        $this->sut = new EnvironmentWarning($this->translatorMock);
    }

    public function testHandleInProduction()
    {
        $this->sut->setEnvironment(Environment::PRODUCTION);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['createFromGlobals'])
            ->getMock();

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContent'])
            ->getMock();

        $next = function () use ($responseMock) {
            return $responseMock;
        };

        $responseMock->expects($this->never())->method('setContent');

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleInTesting()
    {
        $this->sut->setEnvironment(Environment::TESTING);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['createFromGlobals'])
            ->getMock();

        /** @var Response|MockObject $requestMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContent'])
            ->getMock();

        $next = function () use ($responseMock) {
            return $responseMock;
        };

        $responseMock->expects($this->atLeastOnce())->method('setContent');

        $actualResult = $this->sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }
}
