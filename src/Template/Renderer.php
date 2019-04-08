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
     */
    public function addLoader(string $templateType, ILoader $loader)
    {
        $this->loaders[$templateType] = $loader;
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

        $subTemplateIds = $template->parse();

        $subTemplates = $this->loadSubTemplates($subTemplateIds);

        return $template->render($subTemplates);
    }

    /**
     * @param string[][] $subTemplateIds
     *
     * @return string[]
     */
    protected function loadSubTemplates(array $subTemplateIds): array
    {
        if (count($subTemplateIds) === 0) {
            return [];
        }

        $templates = [];
        foreach ($subTemplateIds as $type => $identifiers) {
            $this->assertType($type);

            $loader = $this->loaders[$type];

            /** @var Data[] $entities */
            $entities = $loader->load($identifiers);

            $templates = $this->populateTemplates($type, $entities, $templates);
        }

        return $templates;
    }

    /**
     * @param string $type
     * @param Data[] $entities
     * @param array  $templates
     *
     * @return array
     */
    protected function populateTemplates(string $type, array $entities, array $templates): array
    {
        foreach ($entities as $entity) {
            $vars    = $entity->getVars();
            $content = '';
            foreach ($entity->getTemplates() as $key => $template) {
                $content    = $this->render($template, $vars);
                $vars[$key] = $content;
            }
            $templates[$type][$entity->getIdentifier()] = $content;
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
