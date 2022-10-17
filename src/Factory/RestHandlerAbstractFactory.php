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

    private static array $defaultFormats = [
        'json',
        'csv',
    ];

    public static function setDefaultFormats(array $formats): void
    {
        self::$defaultFormats = $formats;
    }

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
            $formatMatcher = new FormatMatcher(self::$defaultFormats);
        }

        $resource = $container->get($config['resource']);

        return new RestHandler(
            $formatMatcher,
            $container->get($config['serializer'] ?? SerializerInterface::class),
            $resource,
            $container->get(ResponseFactoryInterface::class),
            $config['serialization_context'] ?? [],
            $config['identifier_name'] ?? RestHandler::IDENTIFIER_NAME,
        );
    }
}
