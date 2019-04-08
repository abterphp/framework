<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Filter;

class PrefixFilterTest extends FilterTest
{
    /**
     * @return array
     */
    public function getWhereConditionsProvider(): array
    {
        return [
            'no-params'         => [[], []],
            'no-matching-param' => [['abc' => 'ABC'], []],
            'matching-param'    => [['filter-foo' => 'ABC'], ['foo_field LIKE ?']],
        ];
    }

    /**
     * @dataProvider getWhereConditionsProvider
     *
     * @param array $params
     * @param array $expectedResult
     */
    public function testGetWhereConditions(array $params, array $expectedResult)
    {
        $sut = $this->createFilter();

        $sut->setParams($params);

        $actualResult = $sut->getWhereConditions();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function getQueryParamsProvider(): array
    {
        return [
            'no-params'         => [[], []],
            'no-matching-param' => [['abc' => 'ABC'], []],
            'matching-param'    => [['filter-foo' => 'ABC'], ['ABC%']],
        ];
    }

    /**
     * @dataProvider getQueryParamsProvider
     *
     * @param array $params
     * @param array $expectedResult
     */
    public function testGetQueryParams(array $params, array $expectedResult)
    {
        $sut = $this->createFilter();

        $sut->setParams($params);

        $actualResult = $sut->getQueryParams();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function getQueryPartProvider(): array
    {
        return [
            'no-params'         => [[], ''],
            'no-matching-param' => [['abc' => 'ABC'], ''],
            'matching-param'    => [['filter-foo' => 'ABC'], 'filter-foo=ABC'],
        ];
    }

    /**
     * @dataProvider getQueryPartProvider
     *
     * @param array  $params
     * @param string $expectedResult
     */
    public function testGetQueryPart(array $params, string $expectedResult)
    {
        $sut = $this->createFilter();

        $sut->setParams($params);

        $actualResult = $sut->getQueryPart();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param string $inputName
     * @param string $fieldName
     *
     * @return IFilter
     */
    protected function createFilter($inputName = 'foo', $fieldName = 'foo_field'): IFilter
    {
        return new PrefixFilter($inputName, $fieldName);
    }
}
