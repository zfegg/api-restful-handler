<?php


namespace Zfegg\ApiRestfulHandler\Serializer;


use Laminas\Paginator\Paginator as BasePaginator;
use SimpleSerializer\Paginator\OffsetPaginatorInterface;

class Paginator implements OffsetPaginatorInterface
{
    private BasePaginator $paginator;

    public function __construct(BasePaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function getIterator()
    {
        return $this->paginator->getIterator();
    }

    public function getCurrentPage(): int
    {
        return $this->paginator->getCurrentPageNumber();
    }

    public function getItemsPerPage(): int
    {
        return $this->paginator->getItemCountPerPage();
    }

    public function count()
    {
        return count($this->paginator);
    }
}