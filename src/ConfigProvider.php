<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Mvc\Attribute\FromResource;
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
                    NormalizerInterface::class => Serializer::class,
                ],

                'auto' => [
                    'types' => [
                        ParamResolverManager::class => [
                            'parameters' => [
                                'config' => [
                                    'factories' => [
                                        FromResource::class => Factory\ResourceResolverFactory::class,
                                    ],
                                ]
                            ]
                        ],
                    ]
                ],
            ],
        ];
    }
}
