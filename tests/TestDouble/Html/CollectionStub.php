<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestDouble\Html;

use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;

class CollectionStub extends Node
{
    protected const CONTENT_TYPE = INode::class;
}
