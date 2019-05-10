<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CacheManagerTest extends TestCase
{
    /** @var CacheManager - System Under Test */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CacheManager();

        parent::setUp();
    }

    /**
     * @return FilesystemInterface|MockObject
     */
    protected function createFilesystemMock()
    {
        return $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(['has', 'read', 'write', 'listContents', 'delete', 'getTimestamp'])
            ->getMock();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHasMissingFilesystemThrowsException()
    {
        $path = 'foo.ext';

        $this->sut->has($path);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHasNoMatchingFilesystemThrowsException()
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs,
            function () {
                return false;
            }
        );

        $path = 'foo.ext';

        $this->sut->has($path);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReadMissingFilesystemThrowsException()
    {
        $path = 'foo.ext';

        $this->sut->read($path);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReadNoMatchingFilesystemThrowsException()
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs,
            function () {
                return false;
            }
        );

        $path = 'foo.ext';

        $this->sut->read($path);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWriteMissingFilesystemThrowsException()
    {
        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->write($path, $content);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWriteNoMatchingFilesystemThrowsException()
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs,
            function () {
                return false;
            }
        );

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->write($path, $content);
    }

    public function testWriteReturnsFalseIfWritingFails()
    {
        $fs = $this->createFilesystemMock();

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('write')->willReturn(false);

        $this->sut->write($path, $content);
    }

    public function testWriteReturnsTrueOnSuccess()
    {
        $fs = $this->createFilesystemMock();

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('write')->willReturn(true);

        $actualResult = $this->sut->write($path, $content);

        $this->assertTrue($actualResult);
    }

    public function testWriteCanCheckFilesystem()
    {
        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs1,
            function () {
                return false;
            }
        );
        $this->sut->registerFilesystem(
            $fs2,
            function () {
                return true;
            }
        );

        $path    = 'foo.ext';
        $content = 'bar';

        $fs1->expects($this->never())->method('write')->willReturn(false);
        $fs2->expects($this->once())->method('write')->willReturn(true);

        $actualResult = $this->sut->write($path, $content);

        $this->assertTrue($actualResult);
    }

    public function testGetWebPath()
    {
        $path      = 'foo.ext';
        $timestamp = 'bar';

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('getTimestamp')->with($path)->willReturn($timestamp);

        $actualResult = $this->sut->getWebPath($path);

        $this->assertContains($path, $actualResult);
        $this->assertNotSame($path, $actualResult);
    }

    public function testFlush()
    {
        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs1);
        $this->sut->registerFilesystem($fs2);

        $obj1 = ['path' => 'foo', 'basename' => ''];
        $obj2 = ['path' => 'bar', 'basename' => ''];
        $obj3 = ['path' => 'baz', 'basename' => ''];
        $obj4 = ['path' => 'quix', 'basename' => ''];

        $fs1->expects($this->once())->method('listContents')->willReturn([$obj1, $obj2]);
        $fs2->expects($this->once())->method('listContents')->willReturn([$obj3, $obj4]);

        $fs1->expects($this->exactly(2))->method('delete');
        $fs2->expects($this->exactly(2))->method('delete');

        $this->sut->flush();
    }
}
