<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets\CacheManager;

use League\Flysystem\DirectoryListing;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FlysystemTest extends TestCase
{
    /** @var Flysystem - System Under Test */
    protected Flysystem $sut;

    public function setUp(): void
    {
        $this->sut = new Flysystem();

        parent::setUp();
    }

    /**
     * @return FilesystemOperator|MockObject
     */
    protected function createFilesystemMock()
    {
        return $this->createMock(Filesystem::class);
    }

    public function testHasThrowsExceptionWhenThereAreNoFilesystemsRegistered(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $path = 'foo.ext';

        $this->sut->fileExists($path);
    }

    public function testHasThrowsExceptionWhenNoMatchingFilesystemIsFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs,
            fn () => false
        );

        $path = 'foo.ext';

        $this->sut->fileExists($path);
    }

    public function testFileExistsUsesTheFirstCheckedFilesystem(): void
    {
        $expectedResult = true;

        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs1,
            fn () => false
        );
        $this->sut->registerFilesystem(
            $fs2,
            fn () => true
        );

        $path = 'foo.ext';

        $fs1->expects($this->never())->method('read')->willThrowException(new UnableToReadFile($path));
        $fs2->expects($this->once())->method('fileExists')->willReturn($expectedResult);

        $actualResult = $this->sut->fileExists($path);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testReadThrowsExceptionWhenThereAreNoFilesystemsRegistered(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $path = 'foo.ext';

        $this->sut->read($path);
    }

    public function testReadThrowsExceptionWhenNoMatchingFilesystemIsFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs,
            fn () => false
        );

        $path = 'foo.ext';

        $this->sut->read($path);
    }

    public function testReadReturnsNullIfPathDoesNotExist(): void
    {
        $fs = $this->createFilesystemMock();
        $fs->expects($this->once())->method('fileExists')->willReturn(false);
        $this->sut->registerFilesystem($fs);

        $path = 'foo.ext';

        $actualResult = $this->sut->read($path);

        $this->assertNull($actualResult);
    }

    public function testReadReturnsNullIfFileCanNotBeRead(): void
    {
        $path = 'foo.ext';

        $fs = $this->createFilesystemMock();
        $fs->expects($this->any())->method('fileExists')->willReturn(true);
        $fs->expects($this->once())->method('read')->willThrowException(new UnableToReadFile($path));
        $this->sut->registerFilesystem($fs);

        $actualResult = $this->sut->read($path);

        $this->assertNull($actualResult);
    }

    public function testReadReturnsContentIfFileIsReadable(): void
    {
        $expectedResult = 'bar';

        $fs = $this->createFilesystemMock();
        $fs->expects($this->any())->method('fileExists')->willReturn(true);
        $fs->expects($this->once())->method('read')->willReturn($expectedResult);
        $this->sut->registerFilesystem($fs);

        $path = 'foo.ext';

        $actualResult = $this->sut->read($path);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testReadUsesTheFirstCheckedFilesystem(): void
    {
        $expectedResult = 'bar';

        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs1,
            fn () => false
        );
        $this->sut->registerFilesystem(
            $fs2,
            fn () => true
        );

        $path = 'foo.ext';

        $fs1->expects($this->never())->method('read')->willThrowException(new UnableToReadFile($path));
        $fs2->expects($this->once())->method('fileExists')->willReturn(true);
        $fs2->expects($this->once())->method('read')->willReturn($expectedResult);

        $actualResult = $this->sut->read($path);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testWriteThrowsExceptionWhenThereAreNoFilesystemsRegistered(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->write($path, $content);
    }

    public function testWriteThrowsExceptionWhenNoMatchingFilesystemIsFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs,
            fn () => false
        );

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->write($path, $content);
    }

    public function testWriteReturnsFalseIfWritingFails(): void
    {
        $fs = $this->createFilesystemMock();

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('write')->willThrowException(new UnableToWriteFile($path));

        $this->sut->write($path, $content);
    }

    public function testWriteReturnsTrueOnSuccess(): void
    {
        $fs = $this->createFilesystemMock();

        $path    = 'foo.ext';
        $content = 'bar';

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('write');

        $actualResult = $this->sut->write($path, $content);

        $this->assertTrue($actualResult);
    }

    public function testWriteUsesTheFirstCheckedFilesystem(): void
    {
        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $this->sut->registerFilesystem(
            $fs1,
            fn () => false
        );
        $this->sut->registerFilesystem(
            $fs2,
            fn () => true
        );

        $path    = 'foo.ext';
        $content = 'bar';

        $fs1->expects($this->never())->method('write')->willThrowException(new UnableToWriteFile($path));
        $fs2->expects($this->once())->method('write');

        $actualResult = $this->sut->write($path, $content);

        $this->assertTrue($actualResult);
    }

    public function testWriteReturnsFalseOnFileExistExceptionIfWritingIsNotForced(): void
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $path    = 'foo.ext';
        $content = 'bar';

        $fs->expects($this->once())->method('write')->willThrowException(new UnableToWriteFile($path));

        $actualResult = $this->sut->write($path, $content, false);

        $this->assertFalse($actualResult);
    }

    public function testWriteTriesToDeleteExistingFileIfWritingIsForcedAndFileExists(): void
    {
        $expectedResult = true;

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $path    = 'foo.ext';
        $content = 'bar';

        $fs->expects($this->once())->method('fileExists')->with($path)->willReturn(true);
        $fs->expects($this->once())->method('delete');
        $fs->expects($this->once())->method('write')->with($path, $content);

        $actualResult = $this->sut->write($path, $content);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetWebPath(): void
    {
        $path      = 'foo.ext';
        $timestamp = time();

        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $fs->expects($this->once())->method('lastModified')->with($path)->willReturn($timestamp);

        $actualResult = $this->sut->getWebPath($path);

        $this->assertStringContainsString($path, $actualResult);
        $this->assertNotSame($path, $actualResult);
    }

    public function testFlushDeletesAllFlushableFilesInAllRegisteredFilesystems(): void
    {
        $fs1 = $this->createFilesystemMock();
        $fs2 = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs1);
        $this->sut->registerFilesystem($fs2);

        $obj1 = ['path' => 'foo', 'basename' => ''];
        $obj2 = ['path' => 'bar', 'basename' => ''];
        $obj3 = ['path' => 'baz', 'basename' => ''];
        $obj4 = ['path' => 'quix', 'basename' => ''];

        $fs1->expects($this->once())->method('listContents')->willReturn(new DirectoryListing([$obj1, $obj2]));
        $fs2->expects($this->once())->method('listContents')->willReturn(new DirectoryListing([$obj3, $obj4]));

        $fs1->expects($this->exactly(2))->method('delete');
        $fs2->expects($this->exactly(2))->method('delete');

        $this->sut->flush();
    }

    public function testFlushIgnoresDotGitignoreFiles(): void
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $obj1 = ['path' => 'foo', 'basename' => '.gitignore'];

        $fs->expects($this->once())->method('listContents')->willReturn(new DirectoryListing([$obj1]));

        $fs->expects($this->never())->method('delete');

        $this->sut->flush();
    }

    public function testFlushIgnoresPhpFiles(): void
    {
        $fs = $this->createFilesystemMock();

        $this->sut->registerFilesystem($fs);

        $obj1 = ['path' => 'foo', 'basename' => 'index', 'extension' => 'php'];

        $fs->expects($this->once())->method('listContents')->willReturn(new DirectoryListing([$obj1]));

        $fs->expects($this->never())->method('delete');

        $this->sut->flush();
    }

    public function testFlushUsesSetIsFlushableCallback(): void
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

        $fs1->expects($this->once())->method('listContents')->willReturn(new DirectoryListing([$obj1, $obj2]));
        $fs2->expects($this->once())->method('listContents')->willReturn(new DirectoryListing([$obj3, $obj4]));

        $fs1->expects($this->exactly(2))->method('delete');
        $fs2->expects($this->never())->method('delete');

        $this->sut->flush();
    }
}
