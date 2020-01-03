<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

interface ILoader
{
    /**
     * @param array<string,ParsedTemplate[]> $parsedTemplates
     *
     * @return IData[]
     */
    public function load(array $parsedTemplates): array;

    /**
     * @param string[] $identifiers
     * @param string   $cacheTime
     *
     * @return bool
     */
    public function hasAnyChangedSince(array $identifiers, string $cacheTime): bool;
}
