<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;

class DevCacheManager extends CacheManager
{
    /**
     * @param string $path
     * @param string $groupName
     *
     * @return bool
     */
    public function has(string $path): bool
    {
        return false;
    }
}
