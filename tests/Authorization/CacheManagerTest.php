<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Authorization;

use Opulence\Cache\ICacheBridge;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CacheManagerTest extends TestCase
{
    /** @var CacheManager - System Under Test */
    protected CacheManager $sut;

    /** @var ICacheBridge|MockObject */
    protected $cacheBridgeMock;

    public function setUp(): void
    {
        $this->cacheBridgeMock = $this->createMock(ICacheBridge::class);

        $this->sut = new CacheManager($this->cacheBridgeMock);

        parent::setUp();
    }

    public function testStoreAll(): void
    {
        $data    = ['foo' => 'bar'];
        $payload = '{"foo":"bar"}';

        $this->cacheBridgeMock
            ->expects($this->once())
            ->method('set')
            ->with('casbin_auth_collection', $payload, PHP_INT_MAX);

        $this->sut->storeAll($data);
    }

    public function testGetAllReturnsNullIfCacheBridgeThrowsException(): void
    {
        $this->cacheBridgeMock
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new \Exception());

        $this->sut->getAll();
    }

    /**
     * @return array
     */
    public function getAllProvider(): array
    {
        return [
            ['', null],
            [123, null],
            ['[]', []],
            ['{}', []],
            ['{"foo":"bar"}', ['foo' => 'bar']],
            ['0', [0]],
            ['"0', []],
        ];
    }

    /**
     * @dataProvider getAllProvider
     *
     * @param mixed $payload
     * @param mixed $expectedResult
     */
    public function testGetAll($payload, $expectedResult): void
    {
        $this->cacheBridgeMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($payload);

        $actualResult = $this->sut->getAll();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testClearAllCallsCacheBridge(): void
    {
        $this->cacheBridgeMock
            ->expects($this->once())
            ->method('delete');

        $this->sut->clearAll();
    }
}
