<?php

namespace Zfegg\ApiRestfulHandler\Extension;

use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

class PaginationExtension implements ExtensionInterface
{
    private array $config = [
        'page_size' => 30,
        'page_size_range' => [30, 50, 100, 500]
    ];

    public function __construct(array $config)
    {
        $this->config = $config + $this->config;
    }

    public function getList(Select $select, AbstractTableGateway $table, array $context)
    {
        $adapter = new DbSelect($select, $table->getSql());
        $paginator = new Paginator($adapter);
        $paginator->setCurrentPageNumber($context['page'] ?? 1);
        $paginator->setItemCountPerPage($this->getAllowedPageSize($context['page_size'] ?? null));

        return new \Zfegg\ApiRestfulHandler\Serializer\Paginator($paginator);
    }

    private function getAllowedPageSize(?int $pageSize): int
    {
        if (in_array($pageSize, $this->config['page_size_range'])) {
            return $pageSize;
        }

        return $this->config['page_size'];
    }
}