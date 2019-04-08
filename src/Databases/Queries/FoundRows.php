<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Queries;

use Opulence\Databases\ConnectionPools\ConnectionPool;

class FoundRows
{
    /** @var ConnectionPool */
    protected $connectionPool;

    /**
     * FoundRows constructor.
     *
     * @param ConnectionPool $connectionPool
     */
    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * @return int
     */
    public function get(): int
    {
        $sql = 'SELECT FOUND_ROWS()';

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($sql);

        if (!$statement->execute()) {
            return 0;
        }

        return (int)$statement->fetchColumn();
    }
}
