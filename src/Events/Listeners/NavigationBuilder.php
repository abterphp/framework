<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events\Listeners;

use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\Navigation\IResourcable;
use Casbin\Enforcer;
use Opulence\Sessions\ISession;

class NavigationBuilder
{
    protected string $username;

    protected Enforcer $enforcer;

    /**
     * NavigationBuilder constructor.
     *
     * @param ISession $session
     * @param Enforcer $enforcer
     */
    public function __construct(ISession $session, Enforcer $enforcer)
    {
        $this->username = $session->get(Session::USERNAME);
        $this->enforcer = $enforcer;
    }

    /**
     * @param NavigationReady $event
     *
     * @throws \Opulence\Routing\Urls\URLException
     */
    public function handle(NavigationReady $event)
    {
        $navigation = $event->getNavigation();

        $nodes = $navigation->getExtendedDescendantNodes();

        foreach ($nodes as $node) {
            if (!($node instanceof IResourcable)) {
                continue;
            }

            if (!$node->getResource()) {
                continue;
            }

            if (!$this->enforcer->enforce($this->username, $node->getResource(), $node->getRole())) {
                $node->disable();
            }
        }
    }
}
