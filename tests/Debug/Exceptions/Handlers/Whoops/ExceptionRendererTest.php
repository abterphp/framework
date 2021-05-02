<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Debug\Exceptions\Handlers\Whoops;

use Exception;
use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Whoops\RunInterface;

class ExceptionRendererTest extends TestCase
{
    public function setUp(): void
    {
        ob_start();
    }

    public function tearDown(): void
    {
        ob_clean();
        ob_end_flush();
    }

    public function testGetRunReturnsRunSet(): void
    {
        /** @var MockObject|RunInterface $runMock */
        $runMock = $this->createMock(RunInterface::class);

        $sut = new ExceptionRenderer($runMock, true);

        $actualResult = $sut->getRun();

        $this->assertSame($runMock, $actualResult);
    }

    public function testRenderCallsHandleException(): void
    {
        $isDevelopmentEnvironment = true;

        $exceptionStub = new Exception();

        /** @var MockObject|RunInterface $runMock */
        $runMock = $this->createMock(RunInterface::class);

        $runMock->expects($this->atLeastOnce())->method('handleException')->with($exceptionStub);

        $sut = new ExceptionRenderer($runMock, $isDevelopmentEnvironment);

        $sut->render($exceptionStub);
    }

    public function testRenderInProduction(): void
    {
        $isDevelopmentEnvironment = false;

        $exceptionStub = new Exception();

        /** @var MockObject|RunInterface $runMock */
        $runMock = $this->createMock(RunInterface::class);

        $runMock->expects($this->never())->method('handleException');

        $sut = new ExceptionRenderer($runMock, $isDevelopmentEnvironment);

        $sut->render($exceptionStub);
    }

    public function testRenderHttpExceptionInDevelopmentWithJsonRequest(): void
    {
        $expectedOutputString = 'foo';

        $isDevelopmentEnvironment = false;

        $exceptionStub = new HttpException(505, $expectedOutputString, ['foo' => 'bar']);
        $jsonRequest   = new Request([], [], [], [], [], []);
        $jsonRequest->getHeaders()->add('CONTENT_TYPE', 'application/json');

        /** @var MockObject|RunInterface $runMock */
        $runMock = $this->createMock(RunInterface::class);

        $runMock->expects($this->never())->method('handleException');

        $sut = new ExceptionRenderer($runMock, $isDevelopmentEnvironment);
        $sut->setRequest($jsonRequest);

        $sut->render($exceptionStub);

        $headers = $sut->getHeaders();
        $this->assertCount(3, $headers);
        $this->assertSame('foo:bar', $headers[1][0]);
        $this->assertSame('Content-Type:application/json', $headers[2][0]);

        $this->assertSame($expectedOutputString, ob_get_contents());
    }

    public function testRenderHttpExceptionInDevelopmentWithHttpRequest(): void
    {
        $expectedOutputString = 'foo';

        $isDevelopmentEnvironment = false;

        $exceptionStub = new Exception($expectedOutputString);
        $htmlRequest   = new Request([], [], [], [], [], []);
        $htmlRequest->getHeaders()->add('CONTENT_TYPE', 'text/html');

        /** @var MockObject|RunInterface $runMock */
        $runMock = $this->createMock(RunInterface::class);

        $runMock->expects($this->never())->method('handleException');

        $sut = new ExceptionRenderer($runMock, $isDevelopmentEnvironment);
        $sut->setRequest($htmlRequest);

        $sut->render($exceptionStub);

        $headers = $sut->getHeaders();
        $this->assertCount(2, $headers);
        $this->assertSame('Content-Type:text/html', $headers[1][0]);

        $this->assertStringContainsString($expectedOutputString, ob_get_contents());
    }
}
