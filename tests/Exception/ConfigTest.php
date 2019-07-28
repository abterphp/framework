<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Exception;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConstructCreatesUsefulMessage()
    {
        $className = '\Foo';
        $relatedEnvVars = ['bar' => 'baz'];

        $sut = new Config($className, $relatedEnvVars);

        $this->assertSame(
            'Insufficient configs found while resolving dependency: \Foo (Related environment variables: baz)',
            $sut->getMessage()
        );
    }
}
