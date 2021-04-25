<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Table;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Component\Body;
use AbterPhp\Framework\Grid\Component\Header;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Tag as TagHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\Html\Tag;

class Table extends Tag implements ITable, ITemplater
{
    public const TAG_TABLE = 'table';

    /**
     *   %1$s - thead
     *   %2$s - tbody
     */
    protected const DEFAULT_TEMPLATE = '%1$s%2$s';

    protected const DEFAULT_TAG = self::TAG_TABLE;

    protected Header $header;

    protected Body $body;

    protected string $template = self::DEFAULT_TEMPLATE;

    /**
     * Table constructor.
     *
     * @param Body                         $body
     * @param Header                       $header
     * @param string[]                     $intents
     * @param array<string,Attribute>|null $attributes
     */
    public function __construct(Body $body, Header $header, array $intents = [], ?array $attributes = null)
    {
        $this->body   = $body;
        $this->header = $header;

        parent::__construct(null, $intents, $attributes);
    }

    /**
     * @param string $baseUrl
     *
     * @return string
     */
    public function getSortedUrl(string $baseUrl): string
    {
        return $this->header->getSortedUrl($baseUrl);
    }

    /**
     * @return array
     */
    public function getSortConditions(): array
    {
        return $this->header->getSortConditions();
    }

    /**
     * @return array<string,string>
     */
    public function getSqlParams(): array
    {
        return $this->header->getQueryParams();
    }

    /**
     * @param IStringerEntity[] $entities
     *
     * @return $this
     */
    public function setEntities(array $entities): ITable
    {
        $this->body->setEntities($entities);

        return $this;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate(string $template): INode
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        return array_merge([$this->header, $this->body], $this->getNodes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $thead = (string)$this->header;
        $tbody = (string)$this->body;

        $content = sprintf(
            $this->template,
            $thead,
            $tbody
        );

        return TagHelper::toString($this->tag, $content, $this->attributes);
    }
}
