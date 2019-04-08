<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Constant;

class Event
{
    const AUTH_READY             = 'auth.ready';
    const NAVIGATION_READY       = 'navigation.ready';
    const GRID_READY             = 'grid.ready';
    const FORM_READY             = 'form.ready';
    const TEMPLATE_ENGINE_READY  = 'templateengine.ready';
    const FLUSH_COMMAND_READY    = 'flushcommand.ready';
    const DASHBOARD_READY        = 'dashboard.ready';
    const SECRET_GENERATOR_READY = 'secretgenerator.ready';

    const ENTITY_PRE_CHANGE  = 'entity.change.pre';
    const ENTITY_POST_CHANGE = 'entity.change.post';

    const ENTITY_CREATE = 'create';
    const ENTITY_UPDATE = 'update';
    const ENTITY_DELETE = 'delete';
}
