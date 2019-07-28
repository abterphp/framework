<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Template\Engine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TemplateEngineReadyTest extends TestCase
{
    /** @var Engine|MockObject */
    protected $engineMock;

    /** @var TemplateEngineReady */
    protected $sut;

    public function setUp()
    {
        $this->engineMock = $this->getMockBuilder(Engine::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->sut = new TemplateEngineReady($this->engineMock);
    }

    public function testGetEngine()
    {
        $actualResult = $this->sut->getEngine();

        $this->assertSame($this->engineMock, $actualResult);
    }
}
