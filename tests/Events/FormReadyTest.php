<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Form\Form;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormReadyTest extends TestCase
{
    /** @var FormReady - System Under Test */
    protected $sut;

    /** @var Form|MockObject */
    protected $formMock;

    public function setUp(): void
    {
        $this->formMock = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->sut = new FormReady($this->formMock);

        parent::setUp();
    }

    public function testGetForm()
    {
        $actualResult = $this->sut->getForm();

        $this->assertSame($this->formMock, $actualResult);
    }
}
