<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Action;

use AbterPhp\Framework\Html\Component\Button;
use AbterPhp\Framework\Html\INode;
use Opulence\Orm\IEntity;

class Action extends Button implements IAction
{
    /** @var IEntity */
    protected $entity;

    /** @var array */
    protected $attributeCallbacks = [];

    /**
     * Action constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param array                     $attributes
     * @param array                     $attributeCallbacks
     * @param string|null               $tag
     */
    public function __construct(
        $content,
        array $intents = [],
        array $attributes = [],
        array $attributeCallbacks = [],
        ?string $tag = null
    ) {
        parent::__construct($content, $intents, $attributes, $tag);

        $this->attributeCallbacks = $attributeCallbacks;
    }

    /**
     * @param IEntity $entity
     */
    public function setEntity(IEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        foreach ($this->attributeCallbacks as $key => $callback) {
            $value  = $this->hasAttribute($key) ? $this->getAttribute($key) : null;
            $result = (array)$callback($value, $this->entity);

            $this->setAttribute($key, ...$result);
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
            if (is_object($node)) {
                $nodes[] = clone $node;
            } else {
                $nodes[] = $node;
            }
        }

        return new Action($nodes, $this->intents, $this->attributes, $this->attributeCallbacks, $this->tag);
    }
}
