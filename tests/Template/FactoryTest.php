<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var Factory */
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
