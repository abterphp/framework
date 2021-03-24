<?php

declare(strict_types=1);

namespace AbterPhp\Framework\I18n;

class Translator implements ITranslator
{
    /** @var array */
    protected $translations = [];

    /**
     * Translator constructor.
     *
     * @param array $translations
     */
    public function __construct(array $translations)
    {
        $this->setTranslations($translations);
    }

    /**
     * @param array $translations
     */
    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @param string $key
     * @param string ...$args
     *
     * @return string
     */
    public function translate(string $key, string ...$args): string
    {
        return $this->translateByArgs($key, $args);
    }

    /**
     * @param string $key
     * @param array  ...$args
     *
     * @return bool
     */
    public function canTranslate(string $key, ...$args): bool
    {
        $res = $this->translateByArgs($key, $args);

        if (strpos($res, '{{translation ') !== 0) {
            return true;
        }

        return substr($res, -2) !== '}}';
    }

    /**
     * @param string $key
     * @param array  $args
     *
     * @return string
     */
    protected function translateByArgs(string $key, array $args = []): string
    {
        $pathParts = explode(':', $key);

        $translations = &$this->translations;
        foreach ($pathParts as $pathPart) {
            if (!is_array($translations) || !array_key_exists($pathPart, $translations)) {
                return "{{translation missing: $key}}";
            }

            $translations = &$translations[$pathPart];
        }

        if (!is_string($translations)) {
            return "{{translation is ambiguous: $key}}";
        }

        foreach ($args as $argKey => $argValue) {
            $argTranslation = $this->translateByArgs($argValue);

            if (substr($argTranslation, 0, 2) === '{{') {
                continue;
            }

            $args[$argKey] = $argTranslation;
        }

        return vsprintf($translations, $args);
    }
}
