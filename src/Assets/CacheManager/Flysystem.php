<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets\CacheManager;

use ArrayAccess;
use InvalidArgumentException;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;

class Flysystem implements ICacheManager
{
    protected const ERROR_FILESYSTEM_NOT_FOUND = 'filesystem not found';

    /** @var FilesystemOperator[] */
    protected array $filesystems = [];

    /** @var callable[] callables that can check if a filesystem is suited to be used for a given path */
    protected array $pathCheckers = [];

    /** @var callable used to decide if a file can be deleted or not */
    protected $isFlushable;

    public function __construct()
    {
        $this->isFlushable = function ($obj) {
            if (!is_array($obj) && !($obj instanceof ArrayAccess)) {
                throw new InvalidArgumentException(
                    sprintf("isFlushable requires an array or \ArrayAccess, received object is not: %s", get_class($obj))
                );
            }

            if ($obj['basename'] === '.gitignore') {
                return false;
            }

            if (!empty($obj['extension']) && strtolower($obj['extension']) === 'php') {
                return false;
            }

            return true;
        };
    }

    /**
     * @param callable $isFlushable must expect an array containing file information and return a true if a file is
     *                              flushable
     *
     * @return $this
     */
    public function setIsFlushable(callable $isFlushable): ICacheManager
    {
        $this->isFlushable = $isFlushable;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return FilesystemOperator
     */
    protected function getFilesystem(string $path): FilesystemOperator
    {
        foreach ($this->filesystems as $priority => $filesystem) {
            if (empty($this->pathCheckers[$priority])) {
                return $filesystem;
            }

            if (call_user_func($this->pathCheckers[$priority], $path)) {
                return $filesystem;
            }
        }

        throw new InvalidArgumentException(static::ERROR_FILESYSTEM_NOT_FOUND);
    }

    /**
     * @param FilesystemOperator $filesystem
     * @param callable|null      $checker
     * @param int|null           $priority
     */
    public function registerFilesystem(FilesystemOperator $filesystem, callable $checker = null, ?int $priority = null)
    {
        $priority = $priority === null ? count($this->filesystems) * -1 : $priority;

        $this->filesystems[$priority]  = $filesystem;
        $this->pathCheckers[$priority] = $checker;

        krsort($this->filesystems);
        krsort($this->pathCheckers);
    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws FilesystemException
     */
    public function fileExists(string $path): bool
    {
        $fs = $this->getFilesystem($path);

        return $fs->fileExists($path);
    }

    /**
     * @param string $path
     *
     * @return string|null
     * @throws FilesystemException
     * @throws UnableToReadFile
     */
    public function read(string $path): ?string
    {
        $fs = $this->getFilesystem($path);

        if (!$fs->fileExists($path)) {
            return null;
        }

        try {
            return $fs->read($path);
        } catch (UnableToReadFile $e) {
            return null;
        }
    }

    /**
     * @param string $path
     * @param string $content
     * @param bool   $force
     *
     * @return bool
     * @throws FilesystemException
     * @throws UnableToWriteFile
     */
    public function write(string $path, string $content, bool $force = true): bool
    {
        $fs = $this->getFilesystem($path);

        if ($fs->fileExists($path)) {
            if (!$force) {
                return false;
            }

            try {
                $fs->delete($path);
            } catch (UnableToDeleteFile $e) {
                // This is a noop(), production builds will (should) remove it completely
                // Note that this can happen if the file was removed between the `$fs->fileExists()` and `$fs->delete()` calls
                assert(true);
            }
        }

        try {
            $fs->write($path, $content);
        } catch (UnableToWriteFile $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function getWebPath(string $path): string
    {
        $fs = $this->getFilesystem($path);

        $timestamp = (string)$fs->lastModified($path);

        $path = DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        $rand = substr(md5($timestamp), 0, 5);

        return sprintf('%s?%s', $path, $rand);
    }

    /**
     * @throws FilesystemException
     */
    public function flush()
    {
        foreach ($this->filesystems as $filesystem) {
            $objects = $filesystem->listContents('/', false);

            foreach ($objects->getIterator() as $object) {
                if (!call_user_func($this->isFlushable, $object)) {
                    continue;
                }
                $filesystem->delete($object['path']);
            }
        }
    }
}
