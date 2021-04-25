<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

interface ITemplater
{
    /**
     * @param string $template
     *
     * @return INode
     */
    public function setTemplate(string $template): INode;
}
