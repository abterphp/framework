<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Orm\DataMappers;

use Opulence\Databases\Adapters\Pdo\Connection;
use Opulence\Databases\Adapters\Pdo\Statement;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Databases\IConnection;
use Opulence\Databases\IStatement;
use PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
use PHPUnit\Framework\MockObject\Matcher\InvokedAtIndex;
use PHPUnit\Framework\MockObject\Matcher\InvokedCount;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

abstract class SqlTestCase extends \PHPUnit\Framework\TestCase
{
    const EXPECTATION_ONCE  = -1;
    const EXPECTATION_ANY   = -2;
    const EXPECTATION_NEVER = -4;

    /** @var IConnection|MockObject */
    protected $readConnectionMock;

    /** @var IConnection|MockObject */
    protected $writeConnectionMock;

    /** @var ConnectionPool|MockObject */
    protected $connectionPoolMock;

    public function setUp()
    {
        parent::setUp();

        $this->readConnectionMock  = $this->getReadConnectionMock();
        $this->writeConnectionMock = $this->getWriteConnectionMock();

        $this->connectionPoolMock = $this->getConnectionPoolMock($this->readConnectionMock, $this->writeConnectionMock);
    }

    /**
     * @return IConnection|MockObject
     */
    public function getReadConnectionMock()
    {
        return $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepare', 'read'])
            ->getMock();
    }

    /**
     * @return IConnection|MockObject
     */
    public function getWriteConnectionMock()
    {
        return $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepare'])
            ->getMock();
    }

    /**
     * @param IConnection|null $readConnection
     * @param IConnection|null $writeConnection
     *
     * @return ConnectionPool|MockObject
     */
    public function getConnectionPoolMock(?IConnection $readConnection, ?IConnection $writeConnection)
    {
        $connectionPool = $this->getMockBuilder(ConnectionPool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getReadConnection', 'getWriteConnection', 'setReadConnection', 'setWriteConnection'])
            ->getMock();

        if ($readConnection) {
            $connectionPool->expects($this->any())->method('getReadConnection')->willReturn($readConnection);
        }

        if ($writeConnection) {
            $connectionPool->expects($this->any())->method('getWriteConnection')->willReturn($writeConnection);
        }

        return $connectionPool;
    }

    /**
     * @param MockObject $connectionMock
     * @param string     $sql
     * @param mixed      $returnValue
     * @param int        $at
     */
    protected function prepare(MockObject $connectionMock, string $sql, $returnValue, int $at = self::EXPECTATION_ONCE)
    {
        $connectionMock
            ->expects($this->getExpectation($at))
            ->method('prepare')
            ->with($sql)
            ->willReturn($returnValue);
    }

    /**
     * @param array $expectedData
     * @param array $collection
     */
    protected function assertCollection(array $expectedData, $collection)
    {
        $this->assertNotNull($collection);
        $this->assertInternalType('array', $collection);
        $this->assertCount(count($expectedData), $collection);

        foreach ($collection as $key => $entity) {
            $this->assertEntity($expectedData[$key], $entity);
        }
    }

    /**
     * @param array  $expectedData
     * @param object $entity
     */
    abstract protected function assertEntity(array $expectedData, $entity);

    /**
     * @param array $valuesToBind
     * @param array $rows
     * @param int   $atBindValues
     * @param int   $atExecute
     * @param int   $atRowCount
     * @param int   $atFetchAll
     *
     * @return IStatement|MockObject
     */
    protected function createReadStatement(
        array $valuesToBind,
        array $rows,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE,
        int $atRowCount = self::EXPECTATION_ANY,
        int $atFetchAll = self::EXPECTATION_ONCE
    ) {
        $statement = $this->createStatement();
        $statement->expects($this->getExpectation($atBindValues))->method('bindValues')->with($valuesToBind);
        $statement->expects($this->getExpectation($atExecute))->method('execute')->willReturn(true);
        $statement->expects($this->getExpectation($atRowCount))->method('rowCount')->willReturn(count($rows));
        $statement->expects($this->getExpectation($atFetchAll))->method('fetchAll')->willReturn($rows);

        return $statement;
    }

    /**
     * @param array $valuesToBind
     * @param mixed $returnValue
     * @param int   $atBindValues
     * @param int   $atExecute
     * @param int   $atFetchAll
     * @param bool  $executeResult
     *
     * @return IStatement|MockObject
     */
    protected function createReadColumnStatement(
        array $valuesToBind,
        $returnValue,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE,
        int $atFetchAll = self::EXPECTATION_ONCE,
        bool $executeResult = true
    ) {
        $statement = $this->createStatement();
        $statement->expects($this->getExpectation($atBindValues))->method('bindValues')->with($valuesToBind);
        $statement->expects($this->getExpectation($atExecute))->method('execute')->willReturn($executeResult);
        $statement->expects($this->getExpectation($atFetchAll))->method('fetchColumn')->willReturn($returnValue);

        return $statement;
    }

    /**
     * @param array $values
     * @param int   $atBindValues
     * @param int   $atExecute
     *
     * @return IStatement|MockObject
     */
    protected function createWriteStatement(
        array $values,
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE
    ) {
        $statement = $this->createStatement();
        $statement->expects($this->getExpectation($atBindValues))->method('bindValues')->with($values);
        $statement->expects($this->getExpectation($atExecute))->method('execute')->willReturn(true);

        return $statement;
    }

    /**
     * @param int $atBindValues
     * @param int $atExecute
     *
     * @return IStatement|MockObject
     */
    protected function createWriteStatementWithAny(
        int $atBindValues = self::EXPECTATION_ONCE,
        int $atExecute = self::EXPECTATION_ONCE
    ) {
        $statement = $this->createStatement();
        $statement->expects($this->getExpectation($atBindValues))->method('bindValues')->withAnyParameters();
        $statement->expects($this->getExpectation($atExecute))->method('execute');

        return $statement;
    }

    /**
     * @return IStatement|MockObject
     */
    protected function createStatement()
    {
        /** @var IStatement|MockObject $mock */
        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(['bindValues', 'execute', 'rowCount', 'fetchAll', 'fetchColumn'])
            ->getMock();

        return $statement;
    }

    /**
     * @param int $at
     *
     * @return AnyInvokedCount|InvokedAtIndex|InvokedCount
     */
    protected function getExpectation(int $at)
    {
        switch ($at) {
            case static::EXPECTATION_NEVER:
                return $this->never();
            case static::EXPECTATION_ONCE:
                return $this->once();
            case static::EXPECTATION_ANY:
                return $this->any();
        }

        return $this->at($at);
    }
}
