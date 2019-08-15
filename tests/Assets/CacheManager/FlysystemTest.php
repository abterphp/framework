<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets\CacheManager;

use League\Flysystem\FileExistsException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FlysystemTest extends TestCase
{
    /** @var Flysystem - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new Flysystem();

        parent::setUp();
    }

    /**
     * @return FilesystemInterface|MockObject
     */
    protected function createFilesystemMock()
    {
        return $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'read', 'write', 'listContents', 'delete', 'getTimestamp'])
            ->getMock();
    }

    public function testHasThrowsExceptionWhenThereAreNoFilesystemsRegistered()
    {
        $this->expectException(\InvalidArgumentException::class);

        $path = 'foo.ext';

        $this->sut->has($path);
    }

    public function testHasThrowsExceptionWhenNoMatchingFilesystemIsFound()
    {
        $this->expectException(\InvalidArgumentException::class);

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

    public function testHasUsesTheFirstCheckedFilesystem()
    {
        $expectedResult = true;

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

        $path = 'foo.ext';

        $fs1->expects($this->never())->method('read')->willReturn(false);
        $fs2->expects($this->once())->method('has')->willReturn($expectedResult);

        $actualResult = $this->sut->has($path);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testReadThrowsExceptionWhenThereAreNoFilesystemsRegistered()
    {
        $this->expectException(\InvalidArgumentException::class);

        $path = 'foo.ext';

        $this->sut->read($path);
    }

    public function testReadThrowsExceptionWhenNoMatchingFilesystemIsFound()
    {
        $this->expectException(\InvalidArgumentException::class);

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

    public function testReadReturnsNullIfPathDoesNotExist()
    {
        $fs = $this->createFilesystemMock();
        $fs->expects($this->once())->method('has')->willReturn(false);
        $this->sut->registerFilesystem($fs);

        $path = 'foo.ext';

        $actualResult = $this->sut->read($path);

        $this->assertNull($actualResult);
    }

    public function testReadReturnsNullIfFileCanNotBeRead()
    {
        $fs = $this->createFilesystemMock();
        $fs->expects($this->any())->method('has')->willReturn(true);
        $fs->expects($this->once())->method('read')->willReturn(false);
        $this->sut->registerFilesystem($fs);

        $path = 'foo.ext';

        $actualResult = $this->sut->read($path);

        $this->assertNull($actualResult);
    }

    public function testReadReturnsContentIfFileIsReadable()
    {
        $expectedResult = 'bar';

        $fs = $this->createFilesystemMock();
        $fs->expects($this->any())->method('has')->willReturn(true);
        $fs->expects($this->once())->method('read')->willReturn($expectedResult);
        $this->sut->registerFilesystem($fs);

        $path = 'foo.ext';

        $actualResult = $this->sut->read($path);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testReadUsesTheFirstCheckedFilesystem()
    {
        $expectedResult = 'bar';

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

        $path = 'foo.ext';

        $fs1->expects($this->never())->method('read')->willReturn(false);
        $fs2->expects($this->once())->method('has')->willReturn(true);
        $fs2->expects($this->once())->method('read')->willReturn($expectedResult);

        $actualResult = $this->sut->read($path);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testWriteThrowsExceptionWhenThereAreNoFilesystemsRegistered()
    {
        $this->expectException(\InvalidArgumentException::class);

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->write($path, $content);
    }

    public function testWriteThrowsExceptionWhenNoMatchingFilesystemIsFound()
    {
        $this->expectException(\InvalidArgumentException::class);

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

    public function testWriteUsesTheFirstCheckedFilesystem()
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

    public function testWriteReturnsFalseOnFileExistExceptionIfWritingIsNotForced()
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $path    = 'foo.ext';
        $content = 'bar';

        $fs->expects($this->once())->method('write')->willThrowException(new FileExistsException($path));

        $actualResult = $this->sut->write($path, $content, false);

        $this->assertFalse($actualResult);
    }

    public function testWriteTriesToDeleteExistingFileIfWritingIsForcedAndFileExists()
    {
        $expectedResult = true;

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $path    = 'foo.ext';
        $content = 'bar';

        $fs->expects($this->at(0))->method('write')->willThrowException(new FileExistsException($path));
        $fs->expects($this->once())->method('delete');
        $fs->expects($this->at(2))->method('write')->willReturn($expectedResult);

        $actualResult = $this->sut->write($path, $content, true);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetWebPath()
    {
        $path      = 'foo.ext';
        $timestamp = 'bar';

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('getTimestamp')->with($path)->willReturn($timestamp);

        $actualResult = $this->sut->getWebPath($path);

        $this->assertStringContainsString($path, $actualResult);
        $this->assertNotSame($path, $actualResult);
    }

    public function testFlushDeletesAllFlushableFilesInAllRegisteredFilesystems()
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

    public function testFlushIgnoresDotGitignoreFiles()
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $obj1 = ['path' => 'foo', 'basename' => '.gitignore'];

        $fs->expects($this->once())->method('listContents')->willReturn([$obj1]);

        $fs->expects($this->never())->method('delete');

        $this->sut->flush();
    }

    public function testFlushIgnoresPhpFiles()
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $obj1 = ['path' => 'foo', 'basename' => 'index', 'extension' => 'php'];

        $fs->expects($this->once())->method('listContents')->willReturn([$obj1]);

        $fs->expects($this->never())->method('delete');

        $this->sut->flush();
    }

    public function testFlushUsesSetIsFlushableCallback()
    {
        $this->sut->setIsFlushable(
            function ($obj) {
                if ($obj['path'] === 'protected') {
                    return false;
                }

                return true;
            }
        );

        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs1);
        $this->sut->registerFilesystem($fs2);

        $obj1 = ['path' => 'foo', 'basename' => 'index', 'extension' => 'php'];
        $obj2 = ['path' => 'foo', 'basename' => '.gitignore'];
        $obj3 = ['path' => 'protected', 'basename' => 'abc'];
        $obj4 = ['path' => 'protected', 'basename' => 'cba'];

        $fs1->expects($this->once())->method('listContents')->willReturn([$obj1, $obj2]);
        $fs2->expects($this->once())->method('listContents')->willReturn([$obj3, $obj4]);

        $fs1->expects($this->exactly(2))->method('delete');
        $fs2->expects($this->never())->method('delete');

        $this->sut->flush();
    }
}
