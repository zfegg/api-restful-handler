<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Attribute\FromResource;
use Zfegg\PsrMvc\ParamResolver\ParamResolverManager;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies'       => [
                'abstract_factories' => [
                    Factory\RestHandlerAbstractFactory::class,
                ],
                'factories' => [
                ],
                'aliases' => [
                    SerializerInterface::class => Serializer::class,
                ]
            ],
            ParamResolverManager::class => [
                'factories' => [
                    FromResource::class => Factory\ResourceResolverFactory::class,
                ]
            ]
        ];
    }
}
