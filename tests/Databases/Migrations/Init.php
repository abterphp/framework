<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Databases\QueryFileLoader;
use DateTime;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\Migration;

/**
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
class Init extends Migration
{
    /**
     * @var QueryFileLoader
     */
    protected $queryFileLoader;

    /**
     * @param IConnection $connection The connection to use in the migration
     */
    public function __construct(IConnection $connection, QueryFileLoader $queryFileLoader)
    {
        parent::__construct($connection);

        $this->queryFileLoader = $queryFileLoader;
    }

    /**
     * Gets the creation date, which is used for ordering
     *
     * @return DateTime The date this migration was created
     */
    public static function getCreationDate(): DateTime
    {
        return DateTime::createFromFormat(DateTime::ATOM, '2019-01-17T14:40:59+00:00');
    }

    /**
     * Executes the query that rolls back the migration
     */
    public function down(): void
    {
        $sql       = $this->queryFileLoader->down('init.sql');
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    /**
     * Executes the query that commits the migration
     */
    public function up(): void
    {
        $sql       = $this->queryFileLoader->up('init.sql');
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }
}
