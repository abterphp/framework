<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Authorization;

use AbterPhp\Framework\Authorization\CombinedAdapter;
use AbterPhp\Framework\Authorization\Constant\Role;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Environments\Environment;
use Casbin\Enforcer;
use Casbin\Exceptions\CasbinException;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class EnforcerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            Enforcer::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws CasbinException
     * @throws IocException
     */
    public function registerBindings(IContainer $container): void
    {
        /** @var CombinedAdapter $combinedAdapter */
        $combinedAdapter = $container->resolve(CombinedAdapter::class);

        $modelPath = sprintf("%s/model.conf", Environment::mustGetVar(Env::DIR_AUTH_CONFIG));
        $enforcer  = new Enforcer($modelPath, $combinedAdapter);

        $enforcer->loadPolicy();

        $container->bindInstance(Enforcer::class, $enforcer);

        $this->registerViewFunction($container, $enforcer);
    }

    /**
     * @param IContainer $container
     * @param Enforcer   $enforcer
     *
     * @throws IocException
     */
    private function registerViewFunction(IContainer $container, Enforcer $enforcer): void
    {
        if (!$container->hasBinding(ISession::class)) {
            return;
        }

        /** @var ISession $session */
        $session  = $container->resolve(ISession::class);
        $username = $session->get(Session::USERNAME);

        /** @var ITranspiler $transpiler */
        $transpiler = $container->resolve(ITranspiler::class);
        $transpiler->registerViewFunction('canView', $this->createCanViewViewFunction($username, $enforcer));
    }

    /**
     * @param ?string  $username
     * @param Enforcer $enforcer
     *
     * @return callable
     */
    public function createCanViewViewFunction(?string $username, Enforcer $enforcer): callable
    {
        return fn (string $key) => $enforcer->enforce($username, 'admin_resource_' . $key, Role::READ);
    }
}
