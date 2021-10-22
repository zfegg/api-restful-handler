<?php


namespace Zfegg\ApiRestfulHandler;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Middleware\ResourceFindMiddleware;

class ConfigProvider
{

    public function __invoke()
    {
        return [
            'dependencies'       => [
                'abstract_factories' => [
                    Factory\RestHandlerAbstractFactory::class,
                ],
                'factories' => [
                    ResourceFindMiddleware::class => Factory\ResourceFindMiddlewareFactory::class,
                ],
                'aliases' => [
                    SerializerInterface::class => Serializer::class,
                ]
            ],
        ];
    }
}
