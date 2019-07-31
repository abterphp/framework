<?php

declare(strict_types=1);

namespace AbterPhp\Framework\I18n;

interface ITranslator
{
    /**
     * @param string $key
     * @param string ...$args
     *
     * @return string
     */
    public function translate(string $key, string ...$args): string;

    /**
     * @param string $key
     * @param array  ...$args
     *
     * @return bool
     */
    public function canTranslate(string $key, ...$args): bool;
}
