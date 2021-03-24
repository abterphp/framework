<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Orm\Ids\Generators;

use Opulence\Orm\Ids\Generators\IIdGenerator;
use Opulence\Orm\Ids\Generators\IIdGeneratorRegistry;
use Opulence\Orm\Ids\Generators\UuidV4Generator;

/**
 * Defines the Id generator registry
 */
class IdGeneratorRegistry implements IIdGeneratorRegistry
{
    /** @var IIdGenerator[] The mapping of class names to their Id generators */
    private array $generators = [];

    private ?UuidV4Generator $uuidV4Generator = null;

    /**
     * @return UuidV4Generator
     */
    private function getUuidV4Generator(): UuidV4Generator
    {
        if (!$this->uuidV4Generator) {
            $this->uuidV4Generator = new UuidV4Generator();
        }

        return $this->uuidV4Generator;
    }

    /**
     * @inheritdoc
     */
    public function getIdGenerator(string $className)
    {
        if (!isset($this->generators[$className])) {
            return $this->getUuidV4Generator();
        }

        return $this->generators[$className];
    }

    /**
     * @inheritdoc
     */
    public function registerIdGenerator(string $className, IIdGenerator $generator): void
    {
        $this->generators[$className] = $generator;
    }
}
