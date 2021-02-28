<?php

namespace AbterPhp\Framework\TestDouble\Database;

use InvalidArgumentException;
use Opulence\Databases\Adapters\Pdo\Statement;
use Opulence\Databases\IStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;

class MockStatementFactory
{
    public const EXPECTATION_ONCE  = -1;
    public const EXPECTATION_ANY   = -2;
    public const EXPECTATION_NEVER = -4;

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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
     * @param TestCase $testCase
     * @param int      $at
     *
     * @return InvocationOrder
     */
    public static function getExpectation(TestCase $testCase, int $at): InvocationOrder
    {
        switch ($at) {
            case static::EXPECTATION_NEVER:
                return $testCase->never();
            case static::EXPECTATION_ONCE:
                return $testCase->once();
            case static::EXPECTATION_ANY:
                return $testCase->any();
        }

        throw new InvalidArgumentException(sprintf("getExpectation does not support %d", $at));
    }
}
