<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Views\Builders;

use Opulence\Views\View;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultBuilderTest extends TestCase
{
    /** @var DefaultBuilder - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new DefaultBuilder();
    }

    public function testBuild()
    {
        /** @var View|MockObject $viewMock */
        $viewMock = $this->createMock(View::class);

        $viewMock->expects($this->atLeastOnce())->method('setVar');

        $this->sut->build($viewMock);
    }
}
