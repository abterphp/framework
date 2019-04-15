<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Databases\QueryFileLoader;
use DateTime;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\Migration;

abstract class RawMigration extends Migration
{
    const FILENAME = '';

    const UP   = 'up';
    const DOWN = 'down';

    /** @var string */
    protected $migrationsPath;

    /** @var string */
    protected $driver;

    /**
     * Init constructor.
     *
     * @param IConnection $connection
     * @param string      $migrationsPath
     * @param string      $driver
     */
    public function __construct(IConnection $connection, string $migrationsPath, string $driver)
    {
        parent::__construct($connection);

        $this->migrationsPath = $migrationsPath;
        $this->driver = $driver;
    }

    /**
     * Gets the creation date, which is used for ordering
     *
     * @return DateTime The date this migration was created
     */
    abstract public static function getCreationDate(): DateTime;

    /**
     * Executes the query that rolls back the migration
     */
    public function down(): void
    {
        $sql       = $this->load(static::FILENAME, static::DOWN);
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    /**
     * Executes the query that commits the migration
     */
    public function up(): void
    {
        $sql       = $this->load(static::FILENAME, static::UP);
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    /**
     * @param string $filename
     * @param string $direction
     *
     * @return string
     */
    protected function load(string $filename, string $direction): string
    {
        $fullPath = sprintf('%s/%s/%s/%s', $this->migrationsPath, $this->driver, $direction, $filename);

        return (string)file_get_contents($fullPath);
    }
}
