<?php


namespace Zfegg\ApiRestfulHandler\Paginator;


interface OffsetPaginatorInterface extends PaginatorInterface, \Countable
{
    public function getCurrentPage(): int;

    public function getItemsPerPage(): int;
}
