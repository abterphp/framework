<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class Renderer
{
    const ERROR_INVALID_TEMPLATE_TYPE = 'Unexpected template type: %s';

    /** @var Factory */
    protected $templateFactory;

    /** @var ILoader[] */
    protected $loaders = [];

    /**
     * Renderer constructor.
     *
     * @param Factory $templateFactory
     */
    public function __construct(Factory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    /**
     * @param string  $templateType
     * @param ILoader $loader
     *
     * @return Renderer
     */
    public function addLoader(string $templateType, ILoader $loader): Renderer
    {
        $this->loaders[$templateType] = $loader;

        return $this;
    }

    /**
     * @param string[][] $subTemplates
     * @param string     $date
     *
     * @return bool
     */
    public function hasAllValidLoaders(array $subTemplates, string $date): bool
    {
        foreach ($subTemplates as $type => $identifiers) {
            if (!array_key_exists($type, $this->loaders)) {
                return false;
            }

            if ($this->loaders[$type]->hasAnyChangedSince($identifiers, $date)) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param string   $rawContent
     * @param string[] $vars
     *
     * @return string
     */
    public function render(string $rawContent, array $vars): string
    {
        $template = $this->templateFactory
            ->create($rawContent)
            ->setVars($vars)
            ->setTypes(array_keys($this->loaders));

        $parsedTemplates = $template->parse();

        $subTemplates = $this->loadSubTemplates($parsedTemplates);

        return $template->render($subTemplates);
    }

    /**
     * @param ParsedTemplate[][][] $groupedTemplates
     *
     * @return string[][]
     */
    protected function loadSubTemplates(array $groupedTemplates): array
    {
        if (count($groupedTemplates) === 0) {
            return [];
        }

        $templates = [];
        foreach ($groupedTemplates as $type => $parsedTemplates) {
            $this->assertType($type);

            $loader = $this->loaders[$type];

            $templateData = $loader->load($parsedTemplates);

            $templates = $this->populateTemplates($type, $templateData, $templates);
        }

        return $templates;
    }

    /**
     * @param string  $type
     * @param IData[] $templateData
     * @param array   $templates
     *
     * @return array
     */
    protected function populateTemplates(string $type, array $templateData, array $templates): array
    {
        foreach ($templateData as $templateDataItem) {
            $vars    = $templateDataItem->getVars();
            $content = '';
            foreach ($templateDataItem->getTemplates() as $key => $template) {
                $content    = $this->render($template, $vars);
                $vars[$key] = $content;
            }
            $templates[$type][$templateDataItem->getIdentifier()] = $content;
        }

        return $templates;
    }

    /**
     * @param string $type
     */
    protected function assertType(string $type)
    {
        if (array_key_exists($type, $this->loaders)) {
            return;
        }

        throw new \RuntimeException(sprintf(static::ERROR_INVALID_TEMPLATE_TYPE, $type));
    }
}
