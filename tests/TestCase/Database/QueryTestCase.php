<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestCase\Database;

use Opulence\Databases\Adapters\Pdo\Connection;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Databases\IConnection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class QueryTestCase extends TestCase
{
    /** @var IConnection|MockObject */
    protected $readConnectionMock;

    /** @var IConnection|MockObject */
    protected $writeConnectionMock;

    /** @var ConnectionPool|MockObject */
    protected $connectionPoolMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->readConnectionMock  = $this->getReadConnectionMock();
        $this->writeConnectionMock = $this->getWriteConnectionMock();

        $this->connectionPoolMock = $this->getConnectionPoolMock($this->readConnectionMock, $this->writeConnectionMock);
    }

    /**
     * @return IConnection|MockObject
     */
    protected function getReadConnectionMock()
    {
        return $this->createMock(Connection::class);
    }

    /**
     * @return IConnection|MockObject
     */
    protected function getWriteConnectionMock()
    {
        return $this->createMock(Connection::class);
    }

    /**
     * @param IConnection|null $readConnection
     * @param IConnection|null $writeConnection
     *
     * @return ConnectionPool|MockObject
     */
    protected function getConnectionPoolMock(?IConnection $readConnection, ?IConnection $writeConnection)
    {
        $connectionPool = $this->createMock(ConnectionPool::class);

        if ($readConnection) {
            $connectionPool->expects($this->any())->method('getReadConnection')->willReturn($readConnection);
        }

        if ($writeConnection) {
            $connectionPool->expects($this->any())->method('getWriteConnection')->willReturn($writeConnection);
        }

        return $connectionPool;
    }
}
