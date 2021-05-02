<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Authorization\Constant\Role;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Tag;

class Item extends Tag implements IResourcable
{
    protected const DEFAULT_TAG = Html5::TAG_LI;

    public const INTENT_DROPDOWN = 'dropdown';

    protected ?string $resource = null;

    protected string $role = Role::READ;

    protected bool $enabled = true;

    /**
     * @param string|null $resource
     *
     * @return $this
     */
    public function setResource(?string $resource): IResourcable
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResource(): ?string
    {
        return $this->resource;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole(string $role): IResourcable
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return $this
     */
    public function disable(): IResourcable
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function enable(): IResourcable
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->enabled) {
            return '';
        }

        return parent::__toString();
    }
}
