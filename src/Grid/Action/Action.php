<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Action;

use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Component\Button;
use AbterPhp\Framework\Html\INode;
use Opulence\Orm\IEntity;

class Action extends Button implements IAction
{
    protected ?IEntity $entity = null;

    /** @var array<string,callable> */
    protected array $attributeCallbacks = [];

    /**
     * Action constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param Attributes|null           $attributes
     * @param array<string,callable>    $attributeCallbacks
     * @param string|null               $tag
     */
    public function __construct(
        $content,
        array $intents = [],
        ?Attributes $attributes = null,
        array $attributeCallbacks = [],
        ?string $tag = null
    ) {
        parent::__construct($content, $intents, $attributes, $tag);

        $this->attributeCallbacks = $attributeCallbacks;
    }

    /**
     * @param IEntity $entity
     */
    public function setEntity(IEntity $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        foreach ($this->attributeCallbacks as $key => $callback) {
            $value  = $this->forceGetAttribute($key)->getValue();
            $result = (array)$callback($value, $this->entity);

            $this->setAttribute(new Attribute($key, ...$result));
        }

        return parent::__toString();
    }

    /**
     * @return IAction
     */
    public function duplicate(): IAction
    {
        $nodes = [];
        foreach ($this->nodes as $node) {
            $nodes[] = clone $node;
        }

        return new Action($nodes, $this->intents, $this->attributes, $this->attributeCallbacks, $this->tag);
    }
}
