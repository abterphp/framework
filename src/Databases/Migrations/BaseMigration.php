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
        $filename = sprintf('%s/%s', static::DOWN, static::FILENAME);

        $this->execute($filename);
    }

    /**
     * Executes the query that commits the migration
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function up(): void
    {
        $filename = sprintf('%s/%s', static::UP, static::FILENAME);

        $this->execute($filename);
    }

    /**
     * @param string $filename
     *
     * @throws FileNotFoundException
     */
    protected function execute(string $filename)
    {
        $content = $this->fileFinder->read($filename);
        if (empty($content)) {
            throw new \RuntimeException(sprintf('Empty file or error during reading it: %s', $filename));
        }

        $statement = $this->connection->prepare($content);
        try {
            if (!$statement->execute()) {
                throw new Exception($statement->errorInfo(), $content, get_class($this), $filename);
            }
        } catch (\PDOException $e) {
            throw new Exception($statement->errorInfo(), $content, get_class($this), $filename, '', 0, $e);
        }
    }
}
