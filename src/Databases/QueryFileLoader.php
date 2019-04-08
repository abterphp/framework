<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases;

class QueryFileLoader
{
    const UP   = 'up';
    const DOWN = 'down';

    /**
     * @var string
     */
    protected $migrationsPath;

    /**
     * @var string
     */
    protected $driver;

    /**
     * QueryFileLoader constructor.
     *
     * @param string $migrationsPath
     */
    public function __construct(string $migrationsPath, string $driver)
    {
        $this->migrationsPath = $migrationsPath;
        $this->driver         = $driver;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function up(string $filename): string
    {
        return $this->load($filename, static::UP);
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function down(string $filename): string
    {
        return $this->load($filename, static::DOWN);
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
