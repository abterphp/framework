<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class Template
{
    /** @var string */
    protected $rawContent = '';

    /** @var ParsedTemplate[][][] */
    protected $parsedTemplates = [];

    /** @var string[] */
    protected $vars = [];

    /** @var array */
    protected $types = ['block'];

    /**
     * Template constructor.
     *
     * @param string   $rawContent
     * @param string[] $vars
     * @param string[] $types
     */
    public function __construct(string $rawContent = '', array $vars = [], array $types = ['block'])
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

    /**
     * @return ParsedTemplate[][][]
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
     * Replaces is {{var/xxx}} occurances in the content
     */
    private function replaceVars()
    {
        $matches = [];
        preg_match_all('/\{\{\s*var\/([\w-]+)\s*\}\}/', $this->rawContent, $matches);

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
     * @return ParsedTemplate[][][]
     */
    private function parseTemplates(): array
    {
        $matches = [];
        $pattern = sprintf('/\{\{\s*(%s)\/([\w-]+)(\s+[^}]*)?\s*\}\}/Ums', implode('|', $this->types));
        preg_match_all($pattern, $this->rawContent, $matches);

        $parsedTemplates = [];
        foreach ($matches[0] as $idx => $occurrence) {
            $type       = $matches[1][$idx];
            $identifier = $matches[2][$idx];
            $attributes = $this->parseAttributes($matches[3][$idx]);

            $this->addOccurence($parsedTemplates, $type, $identifier, $attributes, $occurrence);
        }

        return $parsedTemplates;
    }

    /**
     * @param string $rawAttributes
     *
     * @return string[]
     */
    private function parseAttributes(string $rawAttributes): array
    {
        if (trim($rawAttributes) === '') {
            return [];
        }

        $matches = [];
        $pattern = '/\s*([\w_\-]*)\s*\=\s*\"([^"]*)\"\s*/Ums';
        preg_match_all($pattern, $rawAttributes, $matches);

        $attributes = [];
        foreach (array_keys($matches[0]) as $idx) {
            $attributes[$matches[1][$idx]] = $matches[2][$idx];
        }

        return $attributes;
    }

    /**
     * @param ParsedTemplate[][][] &$parsedTemplates
     * @param string                $type
     * @param string                $identifier
     * @param array                 $attributes
     * @param string                $occurence
     */
    private function addOccurence(
        array &$parsedTemplates,
        string $type,
        string $identifier,
        array $attributes,
        string $occurence
    ): void {
        if (!isset($parsedTemplates[$type][$identifier])) {
            $parsedTemplates[$type][$identifier][] = new ParsedTemplate($type, $identifier, $attributes, [$occurence]);

            return;
        }

        foreach ($parsedTemplates[$type][$identifier] as $parsedTemplate) {
            // Note: == is used on purpose here!
            if ($parsedTemplate->getAttributes() != $attributes) {
                continue;
            }

            $parsedTemplate->addOccurence($occurence);

            return;
        }

        $parsedTemplates[$type][$identifier][] = new ParsedTemplate($type, $identifier, $attributes, [$occurence]);
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
                    $content = str_replace($parsedTemplate->getOccurences(), $replace, $content);
                }
            }
        }

        return $content;
    }
}
