<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class Data implements IData
{
    /**
     * @var string
     * identifier of the template
     * given that the object is returned by template loader responsible for "block" templates
     * an identifier of "first-block" would mean that the template will be substituted in
     * other templates having the subtemplate {{block/first-block}}
     */
    protected $identifier = '';

    /**
     * @var string[]
     * variables which are possible to inject into templates, key is important
     * variable subtemplates in templates looks like this {{var/keyName}}
     * where keyName is the name of key in $vars
     */
    protected $vars = [];

    /**
     * @var string[]
     */
    protected $templates = [];

    /**
     * TemplateData constructor.
     *
     * @param string   $identifier
     * @param string[] $vars
     * @param string[] $templates
     */
    public function __construct($identifier = '', $vars = [], $templates = [])
    {
        $this->identifier = $identifier;
        $this->vars       = $vars;
        $this->templates  = $templates;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return Data
     */
    public function setIdentifier(string $identifier): Data
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * @param array $templates
     *
     * @return Data
     */
    public function setTemplates(array $templates): Data
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     *
     * @return Data
     */
    public function setVars(array $vars): Data
    {
        $this->vars = $vars;

        return $this;
    }
}
