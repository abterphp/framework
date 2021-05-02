<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Module;

use AbterPhp\Framework\Constant\Module;
use DirectoryIterator;
use LogicException;

class Loader
{
    protected const MODULE_FILE_NAME = 'abter.php';

    protected const ERROR_MSG_UNRESOLVABLE_DEPENDENCIES = 'Not able to determine module order. Likely circular dependency found.'; // phpcs:ignore

    /** @var string[] */
    protected array $sourceRoots;

    protected string $moduleFileName;

    /**
     * Loader constructor.
     *
     * @param array  $sourceRoots
     * @param string $moduleFileName
     */
    public function __construct(array $sourceRoots, $moduleFileName = '')
    {
        $this->sourceRoots = $sourceRoots;

        $this->moduleFileName = $moduleFileName ?: static::MODULE_FILE_NAME;
    }

    /**
     * @return array<string,mixed>[]
     */
    public function loadModules(): array
    {
        $rawModules = [];
        foreach ($this->findModules() as $path) {
            /** @var array<string,mixed> $rawModule */
            $rawModule = include $path;

            if (empty($rawModule[Module::ENABLED])) {
                continue;
            }

            $rawModules[] = $rawModule;
        }

        if (count($rawModules) === 0) {
            return [];
        }

        return $this->sortModules($rawModules);
    }

    /**
     * @param array<string,mixed>[] $rawModules
     * @param array<string,string>  $sortedIds
     *
     * @return array
     */
    protected function sortModules(array $rawModules, array $sortedIds = []): array
    {
        $sortedModules = [];
        while (!empty($rawModules)) {
            $sortedCount = count($sortedIds);

            foreach ($rawModules as $idx => $rawModule) {
                foreach ($rawModule[Module::DEPENDENCIES] as $dep) {
                    if (!isset($sortedIds[$dep])) {
                        continue 2;
                    }
                }

                $moduleId             = $rawModule[Module::IDENTIFIER];
                $sortedIds[$moduleId] = $moduleId;
                $sortedModules[]      = $rawModule;
                unset($rawModules[$idx]);
            }

            if ($sortedCount === count($sortedIds)) {
                throw new LogicException(static::ERROR_MSG_UNRESOLVABLE_DEPENDENCIES);
            }
        }

        return $sortedModules;
    }

    /**
     * @return array
     */
    protected function findModules(): array
    {
        $paths = [];

        foreach ($this->sourceRoots as $root) {
            if (empty($root)) {
                continue;
            }

            $paths = array_merge($paths, $this->scanDirectories(new DirectoryIterator($root)));
        }

        return $paths;
    }

    /**
     * @param DirectoryIterator $directoryIterator
     *
     * @return array
     */
    protected function scanDirectories(DirectoryIterator $directoryIterator): array
    {
        $paths = [];
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isFile()) {
                continue;
            }
            if ($fileInfo->getFilename() === $this->moduleFileName) {
                return [$fileInfo->getRealPath()];
            }
        }

        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isDir()) {
                continue;
            }

            $paths = array_merge($paths, $this->scanDirectories(new DirectoryIterator($fileInfo->getRealPath())));
        }

        return $paths;
    }
}
