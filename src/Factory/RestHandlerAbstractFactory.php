<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Handler\RestHandler;
use Zfegg\PsrMvc\FormatMatcher;

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

        if (isset($config['formats'])) {
            $formatMatcher = new FormatMatcher($config['formats']);
        } else {
            $formatMatcher = $container->get(FormatMatcher::class);
        }

        return new RestHandler(
            $formatMatcher,
            $container->get(SerializerInterface::class),
            $container->get($config['resource']),
            $container->get(ResponseFactoryInterface::class),
            $config['serialize_context'] ?? []
        );
    }
}
