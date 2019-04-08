<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Factory\Table\BodyFactory as BodyFactory;
use AbterPhp\Framework\Grid\Factory\Table\HeaderFactory as HeaderFactory;
use AbterPhp\Framework\Grid\Table\Table;

class TableFactory
{
    const ATTRIBUTE_CLASS = 'class';

    const ERROR_MSG_BODY_CREATED      = 'Grid table body is already created.';
    const ERROR_MSG_HEADER_CREATED    = 'Grid table header is already created.';
    const ERROR_MSG_TABLE_CREATED     = 'Grid table is already created.';
    const ERROR_MSG_NO_BODY_CREATED   = 'Grid table body is not yet created';
    const ERROR_MSG_NO_HEADER_CREATED = 'Grig table header is not yet created';

    /** @var HeaderFactory */
    protected $headerFactory;

    /** @var BodyFactory */
    protected $bodyFactory;

    /** @var array */
    protected $tableAttributes = [
        self::ATTRIBUTE_CLASS => 'table table-striped table-hover table-bordered',
    ];

    /** @var array */
    protected $headerAttributes = [];

    /** @var array */
    protected $bodyAttributes = [];

    /**
     * TableFactory constructor.
     *
     * @param HeaderFactory $headerFactory
     * @param BodyFactory   $bodyFactory
     */
    public function __construct(HeaderFactory $headerFactory, BodyFactory $bodyFactory)
    {
        $this->headerFactory = $headerFactory;
        $this->bodyFactory   = $bodyFactory;
    }

    /**
     * @param callable[]   $getters
     * @param Actions|null $rowActions
     * @param array        $params
     * @param string       $baseUrl
     *
     * @return Table
     */
    public function create(
        array $getters,
        ?Actions $rowActions,
        array $params,
        string $baseUrl
    ): Table {
        $hasActions = $rowActions && count($rowActions) > 0;

        $header = $this->headerFactory->create($hasActions, $params, $baseUrl);
        $body   = $this->bodyFactory->create($getters, $this->bodyAttributes, $rowActions);

        return new Table($body, $header, [], $this->tableAttributes);
    }
}
