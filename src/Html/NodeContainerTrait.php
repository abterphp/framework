<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\I18n\ITranslator;

// TODO: See if refactoring can help with removing suppressed issues
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
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
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
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn
     *
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
     * @suppress PhanTypeMismatchDeclaredReturn, PhanTypeMismatchReturn, PhanUndeclaredProperty
     *
     * @param ITranslator|null $translator
     *
     * @return INode
     */
    public function setTranslator(?ITranslator $translator): INode
    {
        $this->translator = $translator;

        $nodes = $this->getExtendedNodes();
        foreach ($nodes as $node) {
            $node->setTranslator($translator);
        }

        return $this;
    }
}
