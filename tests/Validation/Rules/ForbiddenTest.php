<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use Countable;
use PHPUnit\Framework\TestCase;

class ForbiddenTest extends TestCase
{
    /**
     * Tests that an empty array fails
     */
    public function testEmptyArrayPasses(): void
    {
        $rule = new Forbidden();
        $this->assertTrue($rule->passes([]));
        $countable = $this->createMock(Countable::class);
        $countable->expects($this->once())
            ->method('count')
            ->willReturn(0);
        $this->assertTrue($rule->passes($countable));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug(): void
    {
        $rule = new Forbidden();
        $this->assertEquals('forbidden', $rule->getSlug());
    }

    /**
     * Tests that a set value passes
     */
    public function testSetValueFails(): void
    {
        $rule = new Forbidden();
        $this->assertFalse($rule->passes(0));
        $this->assertFalse($rule->passes(true));
        $this->assertFalse($rule->passes(false));
        $this->assertFalse($rule->passes('foo'));
    }

    /**
     * Tests that an unset value fails
     */
    public function testUnsetValuePasses(): void
    {
        $rule = new Forbidden();
        $this->assertTrue($rule->passes(null));
        $this->assertTrue($rule->passes(''));
    }
}
