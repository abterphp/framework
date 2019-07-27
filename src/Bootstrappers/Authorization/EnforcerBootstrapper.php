<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Authorization;

use AbterPhp\Framework\Authorization\CacheManager;
use AbterPhp\Framework\Authorization\CombinedAdapter;
use AbterPhp\Framework\Authorization\Constant\Role;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Events\AuthReady;
use Casbin\Enforcer;
use Casbin\Persist\Adapter as AdapterContract;
use CasbinAdapter\Database\Adapter as DatabaseAdapter;
use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Environments\Environment;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class EnforcerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
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
     * @throws \Casbin\Exceptions\CasbinException
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        /** @var CacheManager $cacheManager */
        $cacheManager = $container->resolve(CacheManager::class);

        $model          = $this->createModel($container);
        $defaultAdapter = $this->createDefaultAdapter($container);

        $policyAdapter = $this->createCombinedAdapter($container, $defaultAdapter, $cacheManager);

        $enforcer = new Enforcer($model, $policyAdapter);

        $enforcer->loadPolicy();

        $container->bindInstance(Enforcer::class, $enforcer);

        $this->registerViewFunction($container, $enforcer);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param IContainer $container
     *
     * @return string
     */
    protected function createModel(IContainer $container)
    {
        $dirAuthConfig = Environment::getVar(Env::DIR_AUTH_CONFIG);

        return "$dirAuthConfig/model.conf";
    }

    /**
     * @param IContainer        $container
     * @param AdapterContract   $adapter
     * @param CacheManager|null $cacheManager
     *
     * @return AdapterContract
     * @throws \Opulence\Ioc\IocException
     */
    protected function createCombinedAdapter(
        IContainer $container,
        AdapterContract $adapter,
        CacheManager $cacheManager = null
    ): AdapterContract {
        $eventDispatcher = $container->resolve(IEventDispatcher::class);

        $policyAdapter = new CombinedAdapter($adapter, $cacheManager);
        $eventDispatcher->dispatch(Event::AUTH_READY, new AuthReady($policyAdapter));

        return $policyAdapter;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param IContainer $container
     *
     * @return AdapterContract
     */
    protected function createDefaultAdapter(IContainer $container): AdapterContract
    {
        $driverClass = Environment::getVar('DB_DRIVER') ?: PostgreSqlDriver::class;

        switch ($driverClass) {
            case MySqlDriver::class:
                $dirDriver = 'mysql';
                break;
            case PostgreSqlDriver::class:
                $dirDriver = 'pgsql';
                break;
            default:
                throw new \RuntimeException(
                    "Invalid database driver type specified in environment var \"DB_DRIVER\": $driverClass"
                );
        }

        $config = [
            'type'     => $dirDriver,
            'hostname' => Environment::getVar(Env::DB_HOST),
            'database' => Environment::getVar(Env::DB_NAME),
            'username' => Environment::getVar(Env::DB_USER),
            'password' => Environment::getVar(Env::DB_PASSWORD),
            'hostport' => Environment::getVar(Env::DB_PORT),
        ];

        return DatabaseAdapter::newAdapter($config);
    }

    /**
     * @param IContainer $container
     * @param Enforcer   $enforcer
     *
     * @throws \Opulence\Ioc\IocException
     */
    private function registerViewFunction(IContainer $container, Enforcer $enforcer)
    {
        if (!$container->hasBinding(ISession::class)) {
            return;
        }

        /** @var ISession $session */
        $session  = $container->resolve(ISession::class);
        $username = $session->get(Session::USERNAME);

        /** @var ITranspiler $transpiler */
        $transpiler = $container->resolve(ITranspiler::class);
        $transpiler->registerViewFunction(
            'canView',
            function (string $key) use ($username, $enforcer) {
                return $enforcer->enforce($username, 'admin_resource_' . $key, Role::READ);
            }
        );
    }
}
