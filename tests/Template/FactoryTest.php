<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /** @var Factory - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Factory();
    }

    public function testCreate()
    {
        $rawContent = 'foo';

        $actualResult = $this->sut->create($rawContent);

        $this->assertInstanceOf(Template::class, $actualResult);
        $this->assertSame($rawContent, $actualResult->render([]));
    }
}
