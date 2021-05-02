<?php

declare(strict_types=1);

namespace AbterPhp\Framework\TestDouble\Domain\Entity;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

abstract class FooBarStub implements IStringerEntity
{
    /** @var string */
    protected string $id;

    /** @var string */
    protected string $foo;

    /** @var string */
    protected string $bar;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     *
     * @return $this
     */
    public function setFoo(string $foo): self
    {
        $this->foo = $foo;

        return $this;
    }

    /**
     * @return string
     */
    public function getBar(): string
    {
        return $this->bar;
    }

    /**
     * @param string $bar
     *
     * @return $this
     */
    public function setBar(string $bar): self
    {
        $this->bar = $bar;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->foo;
    }

    /**
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode(
            [
                'foo' => $this->getFoo(),
                'bar' => $this->getBar(),
            ]
        );
    }
}
