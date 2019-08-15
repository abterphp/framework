<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Filesystem;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileFinderTest extends TestCase
{
    /** @var FileFinder - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new FileFinder();

        parent::setUp();
    }

    /**
     * @return FilesystemInterface|MockObject
     */
    protected function createFilesystemMock()
    {
        return $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'read'])
            ->getMock();
    }

    public function testReadWithoutFilesystems()
    {
        $path = 'foo';

        $actualResult = $this->sut->read($path);

        $this->assertNull($actualResult);
    }

    public function testReadWithOnlyDefaultFilesystem()
    {
        $fs = $this->createFilesystemMock();

        $path           = 'foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->any())->method('has')->willReturn(true);
        $fs->expects($this->once())->method('read')->with($path)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadWithOnlyVendorFilesystemImplicit()
    {
        $fs = $this->createFilesystemMock();

        $path           = '/vendor-one/foo';
        $realPath       = '/foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->any())->method('has')->willReturn(true);
        $fs->expects($this->once())->method('read')->with($realPath)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadWithOnlyVendorFilesystemExplicit()
    {
        $fs = $this->createFilesystemMock();

        $path           = 'foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->any())->method('has')->willReturn(true);
        $fs->expects($this->once())->method('read')->with($path)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path, 'vendor-one');

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadRespectsPriorities()
    {
        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $path           = 'foo';
        $expectedResult = 'bar';

        $this->sut->registerFilesystem($fs1);
        $this->sut->registerFilesystem($fs2, 'vendor-one', 1);

        $fs1->expects($this->any())->method('has')->willReturn(true);
        $fs2->expects($this->any())->method('has')->willReturn(true);
        $fs1->expects($this->never())->method('read');
        $fs2->expects($this->once())->method('read')->with($path)->willReturn($expectedResult);

        $actualResult = $this->sut->read($path, 'vendor-one');

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReadSuppressesExceptionsInReading()
    {
        $fs1 = $this->createFilesystemMock();

        $path = 'foo';

        $this->sut->registerFilesystem($fs1, 'vendor-one', 1);

        $fs1->expects($this->any())->method('has')->willReturn(true);
        $fs1->expects($this->once())->method('read')->willThrowException(new \Exception('baz'));

        $actualResult = $this->sut->read($path, 'vendor-one');

        $this->assertNull($actualResult);
    }

    public function testHasWithoutFilesystems()
    {
        $path = 'foo';

        $actualResult = $this->sut->has($path);

        $this->assertFalse($actualResult);
    }

    public function testHasWithOnlyDefaultFilesystem()
    {
        $fs = $this->createFilesystemMock();

        $path = 'foo';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('has')->willReturn(true);

        $actualResult = $this->sut->has($path);

        $this->assertTrue($actualResult);
    }

    public function testHasWithOnlyVendorFilesystemImplicit()
    {
        $fs = $this->createFilesystemMock();

        $path = '/vendor-one/foo';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->once())->method('has')->willReturn(true);

        $actualResult = $this->sut->has($path);

        $this->assertTrue($actualResult);
    }

    public function testHasWithOnlyVendorFilesystemExplicit()
    {
        $fs = $this->createFilesystemMock();

        $path = 'foo';

        $this->sut->registerFilesystem($fs, 'vendor-one', 1);

        $fs->expects($this->once())->method('has')->willReturn(true);

        $actualResult = $this->sut->has($path, 'vendor-one');

        $this->assertTrue($actualResult);
    }
}
