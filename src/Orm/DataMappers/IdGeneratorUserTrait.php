<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Orm\DataMappers;

use Opulence\Orm\Ids\Generators\IIdGenerator;
use Opulence\Orm\Ids\Generators\UuidV4Generator;

trait IdGeneratorUserTrait
{
    /** @var IIdGenerator */
    protected $idGenerator;

    /**
     * @param IIdGenerator $idGenerator
     */
    public function setIdGenerator(IIdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @return IIdGenerator
     */
    protected function getIdGenerator(): IIdGenerator
    {
        if (!$this->idGenerator) {
            $this->idGenerator = new UuidV4Generator();
        }

        return $this->idGenerator;
    }
}
