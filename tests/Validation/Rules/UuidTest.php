<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    /** @var Uuid System Under Test */
    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new Uuid();
    }

    /**
     * @return array
     */
    public function passesProvider(): array
    {
        return [
            'empty'      => ['', [], false],
            'uuid4-1'    => ['631022a3-0452-4bb4-8105-ce4f0a37be94', [], true],
            'uuid4-2'    => ['c513d8b7-79fb-4a65-8f6c-1454068b77d2', [], true],
            'uuid4-3'    => ['5b4789fc-a4db-4724-b2f1-ea5e57db3f43', [], true],
            'uuid4-4'    => ['736b183e-6ec3-4556-996a-3e3ee5de1608', [], true],
            'uuid4-5'    => ['022bfa5a-af6f-48c1-8854-048fa66ff905', [], true],
            'uuid4-6'    => ['b3e0f1e7-3ad0-4794-ac9b-db915ad6bda2', [], true],
            'uuid4-7'    => ['65da83c8-ec37-4676-9578-996cb11f7bc8', [], true],
            'uuid4-8'    => ['03188083-cb1b-4059-9737-704aa93f39b5', [], true],
            'uuid4-9'    => ['f0bad66a-3485-451a-9b66-42cf0cb3389d', [], true],
            'uuid4-10'   => ['c654b41b-516b-47e0-b674-988ce1eced42', [], true],
            'short'      => ['c654b41b-516b-47e0-b674-988ce1eced4', [], false],
            'long'       => ['c654b41b-516b-47e0-b674-988ce1eced422', [], false],
            'wrong-dash' => ['c654b41-b516b-47e0-b674-988ce1eced42', [], false],
        ];
    }

    /**
     * @dataProvider passesProvider
     *
     * @param       $value
     * @param array $allValues
     * @param bool  $expectedResult
     */
    public function testPasses($value, array $allValues, bool $expectedResult)
    {
        $actualResult = $this->sut->passes($value, $allValues);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
