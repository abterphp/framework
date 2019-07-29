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
    /** @var ITranslator|MockObject */
    protected $translatorMock;

    public function setUp()
    {
        $this->translatorMock = MockTranslatorFactory::createSimpleTranslator($this, []);
    }

    public function testHandleInProduction()
    {
        $sut = new EnvironmentWarning($this->translatorMock, Environment::PRODUCTION);

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

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }

    public function testHandleInTesting()
    {
        $sut = new EnvironmentWarning($this->translatorMock, Environment::TESTING);

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

        $actualResult = $sut->handle($requestMock, $next);

        $this->assertSame($responseMock, $actualResult);
    }
}
