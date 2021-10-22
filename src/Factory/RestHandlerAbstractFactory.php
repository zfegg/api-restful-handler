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

    private static $defaultFormats = [
        'json',
        'csv',
    ];

    public function setDefaultFormats(array $formats)
    {
        self::$defaultFormats = $formats;
    }

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

        $negotiator = $container->has(Negotiator::class) ? $container->get(Negotiator::class) : new Negotiator();

        $handler = new RestHandler(
            new FormatMatcher($negotiator, $options['formats'] ?? self::$defaultFormats),
            $container->get(SerializerInterface::class),
            $container->get($options['resource']),
            $container->get(ResponseFactoryInterface::class),
            $options['serialize_context'] ?? []
        );

        return $handler;
    }
}
