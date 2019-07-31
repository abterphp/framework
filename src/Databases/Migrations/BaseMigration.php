<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Filesystem\FileFinder;
use DateTime;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\Migration;

class BaseMigration extends Migration
{
    const FILENAME = 'foo-bar';

    const UP   = 'up';
    const DOWN = 'down';

    /** @var FileFinder */
    protected $fileFinder;

    /**
     * Init constructor.
     *
     * @param IConnection $connection
     * @param FileFinder  $fileFinder
     */
    public function __construct(IConnection $connection, FileFinder $fileFinder)
    {
        parent::__construct($connection);

        $this->fileFinder = $fileFinder;
    }

    /**
     * Gets the creation date, which is used for ordering
     *
     * @return DateTime The date this migration was created
     */
    public static function getCreationDate(): DateTime
    {
        return new DateTime();
    }

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
        return $this->fileFinder->read(sprintf('%s/%s', $direction, $filename));
    }
}
