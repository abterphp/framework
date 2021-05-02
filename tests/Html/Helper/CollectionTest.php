<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\Tag;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @return array
     */
    public function allInstanceOfProvider(): array
    {
        return [
            'empty'               => [[], Node::class, true],
            'one-node-node'       => [[new Node()], Node::class, true],
            'one-node-inode'      => [[new Node()], INode::class, true],
            'first-node-not-tag'  => [[new Node()], INode::class, true],
            'second-node-not-tag' => [[new Tag(), new Node()], Tag::class, false],
        ];
    }

    /**
     * @dataProvider allInstanceOfProvider
     *
     * @param array  $items
     * @param string $className
     * @param bool   $expectedResult
     */
    public function testAllInstanceOf(array $items, string $className, bool $expectedResult): void
    {
        $actualResult = Collection::allInstanceOf($items, $className);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
