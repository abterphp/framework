<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Module;

use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    protected const MODULE_FILE_NAME = 'module.php';

    public function testNoRootsWorks(): void
    {
        $loader = new Loader([]);

        $actual = $loader->loadModules();

        $this->assertEmpty($actual);
    }

    public function testEmptyRootsAreSkipped(): void
    {
        $loader = new Loader(['']);

        $actual = $loader->loadModules();

        $this->assertEmpty($actual);
    }

    public function testEmptyRootWorks(): void
    {
        $loader = new Loader([__DIR__ . '/fixtures/empty'], static::MODULE_FILE_NAME);

        $actual = $loader->loadModules();

        $this->assertEmpty($actual);
    }

    public function testDisabledModulesAreSkipped(): void
    {
        $loader = new Loader([__DIR__ . '/fixtures/disabled-only'], static::MODULE_FILE_NAME);

        $actual = $loader->loadModules();

        $this->assertEmpty($actual);
    }

    public function testSingleNonEmptyWorks(): void
    {
        $loader = new Loader([__DIR__ . '/fixtures/src'], static::MODULE_FILE_NAME);

        $actual = $loader->loadModules();

        $this->assertNotEmpty($actual);
    }

    public function testMultipleRootWorks(): void
    {
        $loader = new Loader([__DIR__ . '/fixtures/src', __DIR__ . '/fixtures/vendor'], static::MODULE_FILE_NAME);

        $actual = $loader->loadModules();

        $this->assertNotEmpty($actual);
    }

    public function testSimpleCircularDependencyFound(): void
    {
        $this->expectException(\LogicException::class);

        $loader = new Loader([__DIR__ . '/fixtures/self-referencing'], static::MODULE_FILE_NAME);

        $loader->loadModules();
    }

    public function testComplexCircularDependencyFound(): void
    {
        $this->expectException(\LogicException::class);

        $loader = new Loader([__DIR__ . '/fixtures/circular-dependency'], static::MODULE_FILE_NAME);

        $loader->loadModules();
    }
}
