<?php


namespace Zfegg\ApiRestfulHandler\Extension;


use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\AbstractTableGateway;

interface ExtensionInterface
{

    /**
     * @return iterable|void
     */
    public function getList(Select $select, AbstractTableGateway $table, array $context);
}
