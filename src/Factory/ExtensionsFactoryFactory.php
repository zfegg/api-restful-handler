<?php


namespace Zfegg\ApiRestfulHandler\Factory;


use Psr\Container\ContainerInterface;
use Zfegg\ApiRestfulHandler\Extension\ExtensionsFactory;
use Zfegg\ApiRestfulHandler\Extension\PaginationExtension;
use Zfegg\ApiRestfulHandler\Extension\QueryParserExtension;

class ExtensionsFactoryFactory
{
    public function __invoke(ContainerInterface $container): ExtensionsFactory
    {
        return new ExtensionsFactory(
            $container,
            [
                'abstract_factories' => [
                    ExtensionAbstractFactory::class,
                ],
                'aliases' =>  [
                    'pagination' => PaginationExtension::class,
                    'query_parser' => QueryParserExtension::class,
                ],
            ]
        );
    }
}
