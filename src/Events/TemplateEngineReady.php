<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Template\Engine;

class TemplateEngineReady
{
    /** @var Engine */
    private $engine;

    /**
     * TemplateEngineReady constructor.
     *
     * @param Engine $adapter
     */
    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
