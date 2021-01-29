<?php


namespace Zfegg\ApiRestfulHandler\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseFactoryInterface;
use Zfegg\ApiRestfulHandler\Handler\RestHandler;
use Zfegg\ApiRestfulHandler\Utils\FormatMatcher;
use Symfony\Component\Serializer\SerializerInterface;

class RestHandlerAbstractFactory implements AbstractFactoryInterface
{

    private CONST DEFAULT_FORMATS = [
        'json' => ['application/json', 'application/*+json'],
    ];

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');

        return isset($config['rest'][$requestedName]);
    }

    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $options = $container->get('config')['rest'][$requestedName];

        return new RestHandler(
            new FormatMatcher($container->get(Negotiator::class), $options['formats'] ?? self::DEFAULT_FORMATS),
            $container->get(SerializerInterface::class),
            $container->get($options['resource']),
            $container->get(ResponseFactoryInterface::class),
            $options['serialize_context'] ?? []
        );
    }
}
