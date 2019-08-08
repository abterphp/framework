<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Filesystem\IFileFinder;
use DateTime;
use Exception;
use League\Flysystem\FileNotFoundException;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\Migration;

class BaseMigration extends Migration
{
    const FILENAME = 'foo-bar';

    const UP   = 'up';
    const DOWN = 'down';

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
     */
    public function down(): void
    {
        $sql       = $this->load(static::FILENAME, static::DOWN);
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    /**
     * Executes the query that commits the migration
     *
     * @throws FileNotFoundException
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
     * @throws FileNotFoundException
     */
    protected function load(string $filename, string $direction): string
    {
        return $this->fileFinder->read(sprintf('%s/%s', $direction, $filename));
    }
}
