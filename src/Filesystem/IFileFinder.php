<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Filesystem;

use League\Flysystem\FilesystemInterface;

interface IFileFinder
{
    public const DEFAULT_KEY = 'root';

    /**
     * @param FilesystemInterface $filesystem
     * @param string              $key
     * @param int                 $priority
     */
    public function registerFilesystem(
        FilesystemInterface $filesystem,
        string $key = self::DEFAULT_KEY,
        int $priority = -1
    );

    /**
     * @param string $path
     * @param string $key
     *
     * @return bool
     */
    public function has(string $path, string $key = IFileFinder::DEFAULT_KEY): bool;

    /**
     * @param string $path
     * @param string $key
     *
     * @return string|null
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function read(string $path, string $key = IFileFinder::DEFAULT_KEY): ?string;
}
