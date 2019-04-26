<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

interface IResourcable
{
    /**
     * @param string|null $resource
     *
     * @return IResourcable
     */
    public function setResource(?string $resource): IResourcable;

    /**
     * @return string|null
     */
    public function getResource(): ?string;

    /**
     * @param string $role
     *
     * @return IResourcable
     */
    public function setRole(string $role): IResourcable;

    /**
     * @return string|null
     */
    public function getRole(): string;
}
