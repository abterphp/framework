<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class CacheData
{
    public const PAYLOAD_KEY_DATE         = 'date';
    public const PAYLOAD_KEY_SUBTEMPLATES = 'subTemplates';

    /** @var string */
    protected $date = '';

    /** @var string[][] */
    protected $subTemplates = [];

    public function __construct()
    {
        $this->date = date('Y-m-d H:i:s');
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setDate(string $date): CacheData
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @param array $subTemplates
     *
     * @return $this
     */
    public function setSubTemplates(array $subTemplates): CacheData
    {
        $this->subTemplates = $subTemplates;

        return $this;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string[][]
     */
    public function getSubTemplates(): array
    {
        return $this->subTemplates;
    }
}
