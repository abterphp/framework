<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

interface ILoader
{
    /**
     * @param string[] $identifiers
     *
     * @return IData[]
     */
    public function load(array $identifiers): array;

    /**
     * @param string[] $identifiers
     * @param string   $cacheTime
     *
     * @return bool
     */
    public function hasAnyChangedSince(array $identifiers, string $cacheTime): bool;
}
