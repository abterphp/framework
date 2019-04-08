<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\I18n\ITranslator;

trait NodeContainerTrait
{

    /**
     * @return INode[]
     */
    abstract public function getNodes(): array;

    /**
     * @return INode[]
     */
    abstract public function getExtendedNodes(): array;

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getDescendantNodes(int $depth = -1): array
    {
        $nodes = [];
        foreach ($this->getNodes() as $v) {
            $nodes[] = $v;
            if ($depth !== 0 && $v instanceof INodeContainer) {
                $nodes = array_merge($nodes, $v->getDescendantNodes($depth - 1));
            }
        }

        return $nodes;
    }
    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getExtendedDescendantNodes(int $depth = -1): array
    {
        $nodes = [];
        foreach ($this->getExtendedNodes() as $v) {
            $nodes[] = $v;
            if ($depth !== 0 && $v instanceof INodeContainer) {
                $nodes = array_merge($nodes, $v->getExtendedDescendantNodes($depth - 1));
            }
        }

        return $nodes;
    }

    /**
     * @param ITranslator|null $translator
     *
     * @return $this
     */
    public function setTranslator(?ITranslator $translator): INode
    {
        $this->translator = $translator;

        $nodes = $this->getExtendedNodes();
        /** @var INode $node */
        foreach ($nodes as $node) {
            $node->setTranslator($translator);
        }

        return $this;
    }
}
