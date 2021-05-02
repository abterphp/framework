<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Bootstrappers\Grid;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Grid\Pagination\Options as PaginationOptions;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class GridBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            PaginationOptions::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container): void
    {
        $defaultPageSize = (int)Environment::mustGetVar(Env::PAGINATION_SIZE_DEFAULT);
        $numberCount     = (int)Environment::mustGetVar(Env::PAGINATION_NUMBER_COUNT);
        $pageSizeOptions = [];
        $rawOptions      = Environment::mustGetVar(Env::PAGINATION_SIZE_OPTIONS);
        foreach (explode(',', $rawOptions) as $pageSizeOption) {
            $pageSizeOptions[] = (int)$pageSizeOption;
        }

        $paginationOptions = new PaginationOptions($defaultPageSize, $pageSizeOptions, $numberCount);

        $container->bindInstance(PaginationOptions::class, $paginationOptions);
    }
}
