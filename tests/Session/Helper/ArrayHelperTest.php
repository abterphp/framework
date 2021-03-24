<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Session\Helper;

use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    /**
     * @return array[]
     */
    public function flattenProvider(): array
    {
        return [
            'empty'   => [[], []],
            'scalar'  => [['error1'], ['error1']],
            'scalars' => [['error1', 'error2'], ['error1', 'error2']],
            'array'   => [[['error1', 'error2']], ['error1', 'error2']],
            'arrays'  => [[['error1', 'error2'], ['error3']], ['error1', 'error2', 'error3']],
            'mixed'   => [[['error1', 'error2'], 'error3'], ['error1', 'error2', 'error3']],
            'deep'    => [[[['error1', 'error2'], 'error3'], 'error4'], ['error1', 'error2', 'error3', 'error4']],
        ];
    }

    /**
     * @dataProvider flattenProvider
     *
     * @param array $errors
     * @param array $expectedResult
     */
    public function testFlatten(array $errors, array $expectedResult): void
    {
        $actualResult = ArrayHelper::flatten($errors);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
