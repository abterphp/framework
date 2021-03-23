<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Table;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Component\Body;
use AbterPhp\Framework\Grid\Component\Header;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITemplater;

class Table extends Component implements ITable, ITemplater
{
    /**
     *   %1$s - thead
     *   %2$s - tbody
     */
    protected const DEFAULT_TEMPLATE = '%1$s%2$s';

    protected const DEFAULT_TAG = self::TAG_TABLE;

    public const TAG_TABLE = 'table';

    /** @var Header */
    protected Header $header;

    /** @var Body */
    protected Body $body;

    /** @var string */
    protected string $template = self::DEFAULT_TEMPLATE;

    /**
     * Table constructor.
     *
     * @param Body     $body
     * @param Header   $header
     * @param string[] $intents
     * @param array    $attributes
     */
    public function __construct(Body $body, Header $header, array $intents = [], array $attributes = [])
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
     * @return array
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

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);

        return $content;
    }
}
