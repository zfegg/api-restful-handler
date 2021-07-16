<?php


namespace Zfegg\ApiRestfulHandler;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ConfigProvider
{

    public function __invoke()
    {
        return [
            'dependencies'       => [
                'abstract_factories' => [
                    Factory\RestHandlerAbstractFactory::class,
                ],
                'aliases' => [
                    SerializerInterface::class => Serializer::class,
                ]
            ],
        ];
    }
}
