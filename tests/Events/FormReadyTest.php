<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Form\Form;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormReadyTest extends TestCase
{
    /** @var Form|MockObject */
    protected $formMock;

    /** @var FormReady */
    protected $sut;

    public function setUp(): void
    {
        $this->formMock = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods([])
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
