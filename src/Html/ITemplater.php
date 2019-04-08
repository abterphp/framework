<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface ITemplater extends INodeContainer
{
    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate(string $template): INode;
}
