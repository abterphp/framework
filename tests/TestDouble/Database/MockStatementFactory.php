<?php

namespace AbterPhp\Framework\TestDouble\Database;

use Opulence\Databases\Adapters\Pdo\Statement;
use Opulence\Databases\IStatement;
use PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
use PHPUnit\Framework\MockObject\Matcher\InvokedAtIndex;
use PHPUnit\Framework\MockObject\Matcher\InvokedCount;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockStatementFactory
{
    const EXPECTATION_ONCE  = -1;
    const EXPECTATION_ANY   = -2;
    const EXPECTATION_NEVER = -4;

    /**
     * @param TestCase $testCase
     * @param array    $valuesToBind
     * @param array    $rows
     * @param int      $atBindValues
     * @param int      $atExecute
     * @param int      $atRowCount
     * @param int      $atFetchAll
     *
     * @return IStatement|MockObject
     */
    public static function createReadStatement(
        TestCase $testCase,
        array $valuesToBind,
        array $rows,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE,
        int $atRowCount = self::EXPECTATION_ANY,
        int $atFetchAll = self::EXPECTATION_ONCE
    ) {
        $statement = self::createStatement($testCase);
        $statement
            ->expects(static::getExpectation($testCase, $atBindValues))
            ->method('bindValues')
            ->with($valuesToBind);
        $statement
            ->expects(static::getExpectation($testCase, $atExecute))
            ->method('execute')
            ->willReturn(true);
        $statement
            ->expects(static::getExpectation($testCase, $atRowCount))
            ->method('rowCount')
            ->willReturn(count($rows));
        $statement
            ->expects(static::getExpectation($testCase, $atFetchAll))
            ->method('fetchAll')
            ->willReturn($rows);

        return $statement;
    }

    /**
     * @param TestCase $testCase
     * @param array    $valuesToBind
     * @param mixed    $returnValue
     * @param int      $atBindValues
     * @param int      $atExecute
     * @param int      $atFetch
     * @param bool     $executeResult
     *
     * @return IStatement|MockObject
     */
    public static function createReadRowStatement(
        TestCase $testCase,
        array $valuesToBind,
        $returnValue,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE,
        int $atFetch = self::EXPECTATION_ONCE,
        bool $executeResult = true
    ) {
        $statement = static::createStatement($testCase);
        $statement
            ->expects(static::getExpectation($testCase, $atBindValues))
            ->method('bindValues')
            ->with($valuesToBind);
        $statement
            ->expects(static::getExpectation($testCase, $atExecute))
            ->method('execute')
            ->willReturn($executeResult);
        $statement
            ->expects(static::getExpectation($testCase, $atFetch))
            ->method('fetch')
            ->willReturn($returnValue);

        return $statement;
    }

    /**
     * @param TestCase $testCase
     * @param array    $valuesToBind
     * @param mixed    $returnValue
     * @param int      $atBindValues
     * @param int      $atExecute
     * @param int      $atFetchColumn
     * @param bool     $executeResult
     *
     * @return IStatement|MockObject
     */
    public static function createReadColumnStatement(
        TestCase $testCase,
        array $valuesToBind,
        $returnValue,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE,
        int $atFetchColumn = self::EXPECTATION_ONCE,
        bool $executeResult = true
    ) {
        $statement = static::createStatement($testCase);
        $statement
            ->expects(static::getExpectation($testCase, $atBindValues))
            ->method('bindValues')
            ->with($valuesToBind);
        $statement
            ->expects(static::getExpectation($testCase, $atExecute))
            ->method('execute')
            ->willReturn($executeResult);
        $statement
            ->expects(static::getExpectation($testCase, $atFetchColumn))
            ->method('fetchColumn')
            ->willReturn($returnValue);

        return $statement;
    }

    /**
     * @param TestCase $testCase
     * @param array    $values
     * @param int      $atBindValues
     * @param int      $atExecute
     *
     * @return IStatement|MockObject
     */
    public static function createWriteStatement(
        TestCase $testCase,
        array $values,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE
    ) {
        $statement = static::createStatement($testCase);
        $statement
            ->expects(static::getExpectation($testCase, $atBindValues))
            ->method('bindValues')
            ->with($values);
        $statement
            ->expects(static::getExpectation($testCase, $atExecute))
            ->method('execute')
            ->willReturn(true);

        return $statement;
    }

    /**
     * @param TestCase $testCase
     * @param array    $values
     * @param array    $errorInfo
     * @param int      $atBindValues
     * @param int      $atExecute
     * @param int      $atErrorInfo
     *
     * @return IStatement|MockObject
     */
    public static function createErrorStatement(
        TestCase $testCase,
        array $values,
        array $errorInfo,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE,
        int $atErrorInfo = self::EXPECTATION_ONCE
    ) {
        $statement = static::createStatement($testCase);
        $statement
            ->expects(static::getExpectation($testCase, $atBindValues))
            ->method('bindValues')
            ->with($values);
        $statement
            ->expects(static::getExpectation($testCase, $atExecute))
            ->method('execute')
            ->willReturn(false);
        $statement
            ->expects(static::getExpectation($testCase, $atErrorInfo))
            ->method('errorInfo')
            ->willReturn($errorInfo);

        return $statement;
    }

    /**
     * @param TestCase $testCase
     * @param int      $atBindValues
     * @param int      $atExecute
     *
     * @return IStatement|MockObject
     */
    public static function createWriteStatementWithAny(
        TestCase $testCase,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE
    ) {
        $statement = static::createStatement($testCase);
        $statement
            ->expects(static::getExpectation($testCase, $atBindValues))
            ->method('bindValues')
            ->withAnyParameters();
        $statement
            ->expects(static::getExpectation($testCase, $atExecute))
            ->method('execute');

        return $statement;
    }

    /**
     * @param TestCase $testCase
     *
     * @return IStatement|MockObject
     */
    public static function createStatement(TestCase $testCase)
    {
        /** @var IStatement|MockObject $mock */
        $statement = $testCase->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $statement;
    }

    /**
     * @param TestCase   $testCase
     * @param MockObject $connectionMock
     * @param string     $sql
     * @param mixed      $returnValue
     * @param int        $at
     */
    public static function prepare(
        TestCase $testCase,
        MockObject $connectionMock,
        string $sql,
        $returnValue,
        int $at = self::EXPECTATION_ONCE
    ) {
        $connectionMock
            ->expects(static::getExpectation($testCase, $at))
            ->method('prepare')
            ->with($sql)
            ->willReturn($returnValue);
    }

    /**
     * @param TestCase $testCase
     * @param int      $at
     *
     * @return AnyInvokedCount|InvokedAtIndex|InvokedCount
     */
    public static function getExpectation(TestCase $testCase, int $at)
    {
        switch ($at) {
            case static::EXPECTATION_NEVER:
                return $testCase->never();
            case static::EXPECTATION_ONCE:
                return $testCase->once();
            case static::EXPECTATION_ANY:
                return $testCase->any();
        }

        return $testCase->at($at);
    }
}
