<?php


namespace Zfegg\ApiRestfulHandler\Extension;


use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Zfegg\ApiRestfulHandler\Utils\QueryParser;

class QueryParserExtension implements ExtensionInterface
{
    private array $config = [];

    private QueryParser $queryParser;

    public function __construct(array $config)
    {
        $this->config = $config + $this->config;
        $this->queryParser = new QueryParser($this->config['filters'] ?? []);
    }

    public function getList(Select $select, AbstractTableGateway $table, array $context)
    {
        $params = $context['query'];
        $this->queryParser->parseSelect($params, $select);
    }
}
