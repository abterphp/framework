<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Filesystem;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToReadFile;

class FileFinder implements IFileFinder
{
    /** @var FilesystemOperator[][][] */
    protected array $filesystems = [];

    /** @var string[] */
    protected array $filesystemKeys = [];

    /**
     * @param FilesystemOperator $filesystem
     * @param string             $key
     * @param int                $priority
     */
    public function registerFilesystem(
        FilesystemOperator $filesystem,
        string $key = IFileFinder::DEFAULT_KEY,
        int $priority = -1
    ) {
        if (empty($this->filesystems[$key][$priority])) {
            $this->filesystems[$key][$priority] = [];
        }

        $this->filesystems[$key][$priority][] = $filesystem;

        krsort($this->filesystems[$key]);

        $this->filesystemKeys[spl_object_id($filesystem)] = $key;
    }

    /**
     * @param string $path
     * @param string $groupName
     *
     * @return bool
     */
    public function fileExists(string $path, string $groupName = IFileFinder::DEFAULT_KEY): bool
    {
        return $this->findFilesystem($path, $groupName) !== null;
    }

    /**
     * @param string $path
     * @param string $key
     *
     * @return string|null
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function read(string $path, string $key = IFileFinder::DEFAULT_KEY): ?string
    {
        $filesystem = $this->findFilesystem($path, $key);
        if (!$filesystem) {
            return null;
        }

        try {
            $filesystemPath = $this->getFilesystemPath($filesystem, $path);

            return (string)$filesystem->read($filesystemPath);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $path
     * @param string $key
     *
     * @return FilesystemOperator|null
     */
    protected function findFilesystem(string $path, string $key): ?FilesystemOperator
    {
        $possibleKeys = $this->getPossibleKeys($path, $key);
        foreach ($possibleKeys as $rootKey) {
            foreach ($this->filesystems[$rootKey] as $filesystems) {
                foreach ($filesystems as $filesystem) {
                    $realPath = $this->getFilesystemPath($filesystem, $path);
                    if ($filesystem->fileExists($realPath)) {
                        return $filesystem;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param FilesystemOperator $filesystem
     * @param string             $path
     *
     * @return string
     */
    protected function getFilesystemPath(FilesystemOperator $filesystem, string $path): string
    {
        $rootKey = $this->filesystemKeys[spl_object_id($filesystem)];
        if ($rootKey === static::DEFAULT_KEY) {
            return $path;
        }

        $fixedPath = ltrim($path, '/');
        if (strpos($fixedPath, $rootKey) === 0) {
            return substr($fixedPath, strlen($rootKey));
        }

        return $path;
    }

    /**
     * @param string $path
     * @param string $key
     *
     * @return string[]
     */
    protected function getPossibleKeys(string $path, string $key): array
    {
        if ($key !== static::DEFAULT_KEY && isset($this->filesystems[$key])) {
            return [$key];
        }

        $fixedPath = ltrim($path, '/');

        $keys = [];
        foreach (array_keys($this->filesystems) as $rootKey) {
            if (substr($fixedPath, 0, strlen($rootKey)) === $rootKey) {
                $keys[] = $rootKey;
            }
        }

        if (isset($this->filesystems[static::DEFAULT_KEY])) {
            $keys[] = static::DEFAULT_KEY;
        }

        return $keys;
    }
}
