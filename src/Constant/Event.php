<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Event
{
    public const AUTH_READY             = 'auth.ready';
    public const NAVIGATION_READY       = 'navigation.ready';
    public const GRID_READY             = 'grid.ready';
    public const FORM_READY             = 'form.ready';
    public const TEMPLATE_ENGINE_READY  = 'templateengine.ready';
    public const FLUSH_COMMAND_READY    = 'flushcommand.ready';
    public const DASHBOARD_READY        = 'dashboard.ready';
    public const SECRET_GENERATOR_READY = 'secretgenerator.ready';

    public const ENTITY_PRE_CHANGE  = 'entity.change.pre';
    public const ENTITY_POST_CHANGE = 'entity.change.post';

    public const ENTITY_CREATE = 'create';
    public const ENTITY_UPDATE = 'update';
    public const ENTITY_DELETE = 'delete';
}
