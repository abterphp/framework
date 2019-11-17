<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Filesystem\IFileFinder;
use DateTime;
use League\Flysystem\FileNotFoundException;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\Migration;

class BaseMigration extends Migration
{
    protected const FILENAME = 'foo-bar';

    protected const UP   = 'up';
    protected const DOWN = 'down';

    /** @var IFileFinder */
    protected $fileFinder;

    /**
     * Init constructor.
     *
     * @param IConnection $connection
     * @param IFileFinder $fileFinder
     */
    public function __construct(IConnection $connection, IFileFinder $fileFinder)
    {
        parent::__construct($connection);

        $this->fileFinder = $fileFinder;
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public static function getCreationDate(): DateTime
    {
        return new DateTime();
    }

    /**
     * Executes the query that rolls back the migration
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function down(): void
    {
        $sql       = $this->load(static::FILENAME, static::DOWN);
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new Exception($statement->errorInfo());
        }
    }

    /**
     * Executes the query that commits the migration
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function up(): void
    {
        $sql       = $this->load(static::FILENAME, static::UP);
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new Exception($statement->errorInfo());
        }
    }

    /**
     * @param string $filename
     * @param string $direction
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected function load(string $filename, string $direction): string
    {
        return $this->fileFinder->read(sprintf('%s/%s', $direction, $filename));
    }
}
