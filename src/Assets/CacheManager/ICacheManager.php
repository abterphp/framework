<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets\CacheManager;

use League\Flysystem\FilesystemOperator;

interface ICacheManager
{
    public const DEFAULT_KEY = 'root';

    /**
     * @param FilesystemOperator $filesystem
     * @param callable|null      $checker
     * @param int                $priority
     */
    public function registerFilesystem(FilesystemOperator $filesystem, callable $checker = null, int $priority = -1);

    /**
     * @param string $path
     *
     * @return bool
     */
    public function fileExists(string $path): bool;

    /**
     * @param string $path
     *
     * @return string|null
     */
    public function read(string $path): ?string;

    /**
     * @param string $path
     * @param string $content
     * @param bool   $force
     *
     * @return bool
     */
    public function write(string $path, string $content, bool $force = true): bool;

    /**
     * @param string $path
     *
     * @return string
     */
    public function getWebPath(string $path): string;

    public function flush();

    /**
     * @param callable $isFlushable must expect an array containing file information and return a true if a file is
     *                              flushable
     *
     * @return $this
     */
    public function setIsFlushable(callable $isFlushable): ICacheManager;
}
