<?php

declare(strict_types = 1);

namespace ZfeggTest\ApiRestfulHandler;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Factory\RestHandlerAbstractFactory;
use ZfeggTest\ApiRestfulHandler\Resource\DemoResource;
use ZfeggTest\ApiRestfulHandler\Resource\NotAllowedResource;

abstract class AbstractTestCase extends TestCase
{

    protected ServiceManager $container;

    protected function setUp(): void
    {
        $this->container = $container = new ServiceManager();

        $config = [
            'dependencies' => [
                'services' => [
                    SerializerInterface::class => new Serializer(
                        [
                        ],
                        [
                            new JsonEncoder(),
                        ]
                    ),
                ],
            ],
            'rest' => [
                'demo.rest' => [
                    'resource' => DemoResource::class,
                ],
                'not-allowed.rest' => [
                    'resource' => NotAllowedResource::class,
                ],
            ]
        ];
        $config = ArrayUtils::merge($config, (new \Zfegg\PsrMvc\ConfigProvider())());
        $config = ArrayUtils::merge($config, (new \Zfegg\ApiRestfulHandler\ConfigProvider())());
        $config = ArrayUtils::merge($config, (new \Laminas\Diactoros\ConfigProvider())());
        $container->configure($config['dependencies']);
        $container->addAbstractFactory(RestHandlerAbstractFactory::class);
        $container->setService('config', $config);
        $container->setService(DemoResource::class, new DemoResource);
        $container->setService(NotAllowedResource::class, new NotAllowedResource);
    }
}
