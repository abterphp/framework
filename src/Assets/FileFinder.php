<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use League\Flysystem\FilesystemInterface;

class FileFinder implements IFileFinder
{
    /** @var FilesystemInterface[][][] */
    protected $filesystems = [];

    /** @var string[] */
    protected $filesystemKeys = [];

    /**
     * @param FilesystemInterface $filesystem
     * @param string              $key
     * @param int                 $priority
     */
    public function registerFilesystem(
        FilesystemInterface $filesystem,
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
     * @param string $key
     *
     * @return bool
     */
    public function has(string $path, string $groupName = IFileFinder::DEFAULT_KEY): bool
    {
        return $this->findFilesystem($path, $groupName) !== null;
    }

    /**
     * @param string $path
     * @param string $key
     *
     * @return string|null
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function read(string $path, string $groupName = IFileFinder::DEFAULT_KEY): ?string
    {
        $filesystem = $this->findFilesystem($path, $groupName);
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
     * @return FilesystemInterface|null
     */
    protected function findFilesystem(string $path, string $key): ?FilesystemInterface
    {
        $possibleKeys = $this->getPossibleKeys($path, $key);
        foreach ($possibleKeys as $rootKey) {
            foreach ($this->filesystems[$rootKey] as $filesystems) {
                foreach ($filesystems as $filesystem) {
                    $realPath = $this->getFilesystemPath($filesystem, $path);
                    if ($filesystem->has($realPath)) {
                        return $filesystem;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param FilesystemInterface $filesystem
     * @param string              $path
     *
     * @return string
     */
    protected function getFilesystemPath(FilesystemInterface $filesystem, string $path): string
    {
        if (empty($this->filesystemKeys[spl_object_id($filesystem)])) {
            return $path;
        }

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
