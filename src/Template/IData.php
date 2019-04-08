<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

interface IData
{
    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @return string[]
     */
    public function getTemplates(): array;

    /**
     * @return string[]
     */
    public function getVars(): array;
}
