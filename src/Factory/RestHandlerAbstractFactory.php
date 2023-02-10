<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Zfegg\ApiRestfulHandler\RestHandler;
use Zfegg\PsrMvc\Preparer\ResultPreparableInterface;

class RestHandlerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        $config = $container->get('config');

        return isset($config['rest'][$requestedName]);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): RestHandler {
        $config = $container->get('config')['rest'][$requestedName];

        $resource = $container->get($config['resource']);

        return new RestHandler(
            $container->get($config['preparer'] ?? ResultPreparableInterface::class),
            $resource,
            $config['options'] ?? $config['serialization_context'] ?? [],
            $config['identifier_name'] ?? RestHandler::IDENTIFIER_NAME,
        );
    }
}
