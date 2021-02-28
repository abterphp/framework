<?php

namespace AbterPhp\Framework\Filesystem;

use League\Flysystem\Filesystem;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Http\Requests\UploadException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UploaderTest extends TestCase
{
    /** @var Uploader - System Under Test */
    protected $sut;

    /** @var Filesystem|MockObject */
    protected $filesystemMock;

    /** @var string */
    protected $fileManagerPath = '/root/to/path';

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemMock = $this->createMock(Filesystem::class);

        $this->sut = new Uploader($this->filesystemMock, $this->fileManagerPath);
    }

    public function testPersistWithoutFileData()
    {
        $actualResult = $this->sut->persist([]);

        $this->assertEquals([], $actualResult);
    }

    public function testPersist()
    {
        $fileType = 'foo';

        $uploadedFileMock = $this->createMock(UploadedFile::class);

        $fileData            = [];
        $fileData[$fileType] = $uploadedFileMock;

        $actualResult = $this->sut->persist($fileData);

        $this->assertArrayHasKey($fileType, $actualResult);
    }

    public function testPersistPopulatesErrorsIfUploadedFileThrowsException()
    {
        $fileType = 'foo';
        $msg      = 'bar';

        $uploadedFileMock = $this->createMock(UploadedFile::class);

        $uploadedFileMock
            ->expects($this->atLeastOnce())
            ->method('move')
            ->willThrowException(new UploadException($msg));

        $fileData            = [];
        $fileData[$fileType] = $uploadedFileMock;

        $actualResult = $this->sut->persist($fileData);

        $this->assertEquals([], $actualResult);
        $this->assertEquals([$fileType => $msg], $this->sut->getErrors());
    }

    public function testDeleteReturnsFalseIfPathDoesNotExist()
    {
        $path = 'example.foo';

        $this->filesystemMock->expects($this->once())->method('fileExists')->willReturn(false);
        $this->filesystemMock->expects($this->never())->method('delete');

        $actualResult = $this->sut->delete($path);

        $this->assertFalse($actualResult);
    }

    public function testDeleteReturnsFalseIfFileSystemThrowsException()
    {
        $path = 'example.foo';

        $exception = new UnableToDeleteFile($path);

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('delete')->willThrowException($exception);

        $actualResult = $this->sut->delete($path);

        $this->assertFalse($actualResult);
    }

    public function testDelete()
    {
        $path = 'example.foo';

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('delete');

        $actualResult = $this->sut->delete($path);

        $this->assertTrue($actualResult);
    }

    public function testGetContentReturnsNullIfPathDoesNotExist()
    {
        $path = 'example.foo';

        $this->filesystemMock->expects($this->once())->method('fileExists')->willReturn(false);
        $this->filesystemMock->expects($this->never())->method('read');

        $actualResult = $this->sut->getContent($path);

        $this->assertNull($actualResult);
    }

    public function testGetContentReturnsFalseIfFileSystemThrowsException()
    {
        $path = 'example.foo';

        $exception = new UnableToReadFile($path);

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('read')->willThrowException($exception);

        $actualResult = $this->sut->getContent($path);

        $this->assertNull($actualResult);
    }

    public function testGetContent()
    {
        $path    = 'example.foo';
        $content = 'This is the content';

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('read')->willReturn($content);

        $actualResult = $this->sut->getContent($path);

        $this->assertEquals($content, $actualResult);
    }

    public function testGetContentReturnsNullIfReadingFails()
    {
        $path      = 'example.foo';
        $exception = UnableToReadFile::fromLocation($path);

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('read')->willThrowException($exception);

        $actualResult = $this->sut->getContent($path);

        $this->assertNull($actualResult);
    }

    public function testGetStreamReturnsFalselIfPathDoesNotExist()
    {
        $path = 'example.foo';

        $this->filesystemMock->expects($this->once())->method('fileExists')->willReturn(false);
        $this->filesystemMock->expects($this->never())->method('readStream');

        $actualResult = $this->sut->getStream($path);

        $this->assertFalse($actualResult);
    }

    public function testGetStreamReturnsFalseIfFileSystemThrowsException()
    {
        $path = 'example.foo';

        $exception = new UnableToReadFile($path);

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('readStream')->willThrowException($exception);

        $actualResult = $this->sut->getStream($path);

        $this->assertFalse($actualResult);
    }

    public function testGetStream()
    {
        $path    = 'example.foo';
        $content = "foo";

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('readStream')->willReturn($content);

        $actualResult = $this->sut->getStream($path);

        $this->assertEquals($content, $actualResult);
    }

    public function testGetSizeReturnsNullIfPathDoesNotExist()
    {
        $path = 'example.foo';

        $this->filesystemMock->expects($this->once())->method('fileExists')->willReturn(false);
        $this->filesystemMock->expects($this->never())->method('fileSize');

        $actualResult = $this->sut->getSize($path);

        $this->assertNull($actualResult);
    }

    public function testGetSizeReturnsNullIfFileSystemThrowsException()
    {
        $path = 'example.foo';

        $exception = new UnableToRetrieveMetadata($path);

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('fileSize')->willThrowException($exception);

        $actualResult = $this->sut->getSize($path);

        $this->assertNull($actualResult);
    }

    public function testGetSizeReturnsNullIfSizeIsNotAvailable()
    {
        $path = 'example.foo';
        $exception = UnableToRetrieveMetadata::create($path, 'size');

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('fileSize')->willThrowException($exception);

        $actualResult = $this->sut->getSize($path);

        $this->assertNull($actualResult);
    }

    public function testGetSize()
    {
        $path = 'example.foo';
        $size = 3;

        $this->filesystemMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->filesystemMock->expects($this->once())->method('fileSize')->willReturn($size);

        $actualResult = $this->sut->getSize($path);

        $this->assertEquals($size, $actualResult);
    }

    public function testUploaderWillUseRootDirectoryByDefault()
    {
        $this->sut = new Uploader($this->filesystemMock);

        $key  = 'foo';
        $path = 'example.foo';

        $expectedResult = '/example.foo';

        $actualResult = $this->sut->getPath($key, $path);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testUploaderWillUsePathFactoryIfSet()
    {
        $this->sut = new Uploader($this->filesystemMock);

        $fooPathFactory = function ($filename) {
            return "/foo-base-dir/$filename";
        };

        $key  = 'foo';
        $path = 'example.foo';

        $this->sut->setPathFactory($key, $fooPathFactory);

        $expectedResult = '/foo-base-dir/example.foo';

        $actualResult = $this->sut->getPath($key, $path);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
