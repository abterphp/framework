<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Databases\QueryFileLoader;
use PHPUnit\Framework\TestCase;

class QueryFileLoaderTest extends TestCase
{
    public function testDown()
    {
        $migrationsPath = __DIR__ . '/fixtures';
        $driver         = 'foo';
        $filename       = 'test.sql';
        $expectedResult = 'SELECT -1;';

        $sut = $this->createSut($migrationsPath, $driver);

        $actualResult = $sut->down($filename);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testUp()
    {
        $migrationsPath = __DIR__ . '/fixtures';
        $driver         = 'foo';
        $filename       = 'test.sql';
        $expectedResult = 'SELECT 1;';

        $sut = $this->createSut($migrationsPath, $driver);

        $actualResult = $sut->up($filename);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string $migrationsPath
     * @param string $driver
     *
     * @return QueryFileLoader
     */
    protected function createSut(string $migrationsPath, string $driver): QueryFileLoader
    {
        return new QueryFileLoader($migrationsPath, $driver);
    }
}
