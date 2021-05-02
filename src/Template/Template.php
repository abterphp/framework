<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;

class Template
{
    public const TYPE_BLOCK = 'block';

    private const REGEXP_ATTRIBUTES = '/\s*([\w_\-]*)\s*\=\s*\"([^"]*)\"\s*/Ums';
    private const REGEXP_TEMPLATES  = '/\{\{\s*(TYPES)\/([\w-]+)(\s+[^}]*)?\s*\}\}/Ums';
    private const REGEXP_VARIABLES  = '/{{\s*var\/([\w-]+)\s*}}/';

    protected string $rawContent = '';

    /** @var array<string,array<string,ParsedTemplate[]>> */
    protected array $parsedTemplates = [];

    /** @var array<string,string> */
    protected array $vars = [];

    /** @var string[] */
    protected array $types = [self::TYPE_BLOCK];

    /**
     * Template constructor.
     *
     * @param string   $rawContent
     * @param string[] $vars
     * @param string[] $types
     */
    public function __construct(string $rawContent = '', array $vars = [], array $types = [self::TYPE_BLOCK])
    {
        $this->rawContent = $rawContent;
        $this->vars       = $vars;
        $this->types      = $types;
    }

    /**
     * @param string $rawContent
     *
     * @return Template
     */
    public function setRawContent(string $rawContent): Template
    {
        $this->rawContent = $rawContent;

        return $this;
    }

    /**
     * @param string[] $vars
     *
     * @return Template
     */
    public function setVars(array $vars): Template
    {
        $this->vars = $vars;

        return $this;
    }

    /**
     * @param string[] $types
     *
     * @return Template
     */
    public function setTypes(array $types): Template
    {
        $this->types = $types;

        return $this;
    }

    /** @return array<string,array<string,ParsedTemplate[]>>
     */
    public function parse(): array
    {
        if (!$this->rawContent) {
            return [];
        }

        $this->replaceVars();

        $this->parsedTemplates = $this->parseTemplates();

        return $this->parsedTemplates;
    }

    /**
     * Replaces is {{var/xxx}} occurrences in the content
     */
    private function replaceVars(): void
    {
        $matches = [];
        preg_match_all(self::REGEXP_VARIABLES, $this->rawContent, $matches);

        foreach ($matches[1] as $idx => $varName) {
            $search      = $matches[0][$idx];
            $replaceWith = '';
            if (array_key_exists($varName, $this->vars)) {
                $replaceWith = $this->vars[$varName];
            }

            $this->rawContent = str_replace($search, $replaceWith, $this->rawContent);
        }
    }

    /**
     * @return array<string,array<string,ParsedTemplate[]>>
     */
    private function parseTemplates(): array
    {
        $matches = [];
        $pattern = str_replace('TYPES', implode('|', $this->types), self::REGEXP_TEMPLATES);
        preg_match_all($pattern, $this->rawContent, $matches);

        $parsedTemplates = [];
        foreach ($matches[0] as $idx => $occurrence) {
            $type       = $matches[1][$idx];
            $identifier = $matches[2][$idx];
            $attributes = $this->parseAttributes($matches[3][$idx]);

            $this->addOccurrence($parsedTemplates, $type, $identifier, $attributes, $occurrence);
        }

        return $parsedTemplates;
    }

    /**
     * @param string $rawAttributes
     *
     * @return array<string,Attribute>|null
     */
    private function parseAttributes(string $rawAttributes): ?array
    {
        if (trim($rawAttributes) === '') {
            return null;
        }

        $matches = [];
        preg_match_all(self::REGEXP_ATTRIBUTES, $rawAttributes, $matches);

        $attributes = [];
        foreach (array_keys($matches[0]) as $idx) {
            $attributes[$matches[1][$idx]] = $matches[2][$idx];
        }

        return Attributes::fromArray($attributes);
    }

    /**
     * @param array<string,array<string,ParsedTemplate[]>> &$parsedTemplates
     * @param string                                        $type
     * @param string                                        $identifier
     * @param array<string,Attribute>|null                  $attributes
     * @param string                                        $occurrence
     */
    private function addOccurrence(
        array &$parsedTemplates,
        string $type,
        string $identifier,
        ?array $attributes,
        string $occurrence
    ): void {
        if (!isset($parsedTemplates[$type][$identifier])) {
            $parsedTemplates[$type][$identifier][] = new ParsedTemplate($type, $identifier, $attributes, [$occurrence]);

            return;
        }

        foreach ($parsedTemplates[$type][$identifier] as $parsedTemplate) {
            // Note: == is used on purpose here!
            if (Attributes::isEqual($parsedTemplate->getAttributes(), $attributes)) {
                $parsedTemplate->addOccurrence($occurrence);
                return;
            }
        }

        $parsedTemplates[$type][$identifier][] = new ParsedTemplate($type, $identifier, $attributes, [$occurrence]);
    }

    /**
     * @param string[][] $subTemplateValues
     *
     * @return string
     */
    public function render(array $subTemplateValues): string
    {
        $content = $this->rawContent;

        foreach ($this->parsedTemplates as $type => $typeTemplates) {
            if (!array_key_exists($type, $subTemplateValues)) {
                $subTemplateValues[$type] = [];
            }
            foreach ($typeTemplates as $identifier => $identifierTemplates) {
                $replace = '';
                if (array_key_exists($identifier, $subTemplateValues[$type])) {
                    $replace = $subTemplateValues[$type][$identifier];
                }

                foreach ($identifierTemplates as $parsedTemplate) {
                    $content = str_replace($parsedTemplate->getOccurrences(), $replace, $content);
                }
            }
        }

        return $content;
    }
}
