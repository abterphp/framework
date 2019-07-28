<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets\CacheManager;

use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;

class Flysystem implements ICacheManager
{
    const ERROR_FILESYSTEM_NOT_FOUND = 'filesystem not found';

    /** @var FilesystemInterface[] */
    protected $filesystems = [];

    /** @var callable[] callables that can check if a filesystem is suited to be used for a given path */
    protected $pathCheckers = [];

    /** @var callable used to decide if a file can be deleted or not */
    protected $isFlushable;

    public function __construct()
    {
        $this->isFlushable = function (array $obj) {
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
     * @return FilesystemInterface
     */
    protected function getFilesystem(string $path): FilesystemInterface
    {
        foreach ($this->filesystems as $priority => $filesystem) {
            if (empty($this->pathCheckers[$priority])) {
                return $filesystem;
            }

            if (call_user_func($this->pathCheckers[$priority], $path)) {
                return $filesystem;
            }
        }

        throw new \InvalidArgumentException(static::ERROR_FILESYSTEM_NOT_FOUND);
    }

    /**
     * @param FilesystemInterface $filesystem
     * @param callable|null       $checker
     * @param int|null            $priority
     */
    public function registerFilesystem(FilesystemInterface $filesystem, callable $checker = null, ?int $priority = null)
    {
        $priority = $priority === null ? count($this->filesystems) * -1 : $priority;

        $this->filesystems[$priority]  = $filesystem;
        $this->pathCheckers[$priority] = $checker;

        krsort($this->filesystems);
        krsort($this->pathCheckers);
    }

    /**
     * @param string $path
     * @param string $groupName
     *
     * @return bool
     */
    public function has(string $path): bool
    {
        $fs = $this->getFilesystem($path);

        return $fs->has($path);
    }

    /**
     * @param string $path
     *
     * @return string|null
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function read(string $path): ?string
    {
        $fs = $this->getFilesystem($path);

        if (!$fs->has($path)) {
            return null;
        }

        $content = $fs->read($path);
        if ($content === false) {
            return null;
        }

        return (string)$content;
    }

    /**
     * @param string $path
     * @param string $content
     * @param string $force
     *
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     */
    public function write(string $path, string $content, bool $force = true): bool
    {
        $fs = $this->getFilesystem($path);

        try {
            return (bool)$fs->write($path, $content);
        } catch (FileExistsException $e) {
            if ($force) {
                $fs->delete($path);

                return (bool)$fs->write($path, $content);
            }
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getWebPath(string $path): string
    {
        $fs = $this->getFilesystem($path);

        $timestamp = (string)$fs->getTimestamp($path);

        $path = '/' . ltrim($path, '/');
        $rand = substr(md5($timestamp), 0, 5);

        return sprintf('%s?%s', $path, $rand);
    }

    /**
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function flush()
    {
        foreach ($this->filesystems as $filesystem) {
            $objects = $filesystem->listContents('/', false);

            foreach ($objects as $object) {
                if (!call_user_func($this->isFlushable, $object)) {
                    continue;
                }
                $filesystem->delete($object['path']);
            }
        }
    }
}
