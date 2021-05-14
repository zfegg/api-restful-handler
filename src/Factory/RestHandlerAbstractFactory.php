<?php


namespace Zfegg\ApiRestfulHandler\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareFactory;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseFactoryInterface;
use Zfegg\ApiRestfulHandler\Handler\RestHandler;
use Zfegg\ApiRestfulHandler\Middleware\ResourceFindMiddleware;
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

        $negotiator = $container->has(Negotiator::class) ? $container->get(Negotiator::class) : new Negotiator();

        $handler = new RestHandler(
            new FormatMatcher($negotiator, $options['formats'] ?? self::DEFAULT_FORMATS),
            $container->get(SerializerInterface::class),
            $container->get($options['resource']),
            $container->get(ResponseFactoryInterface::class),
            $options['serialize_context'] ?? []
        );

//        if (isset($options['parent_resources'])) {
//            foreach ($options['parent_resources'] as $name => &$item) {
//                if (is_string($item)) {
//                    $item = ['resource' => $item, 'attr' => $name];
//                }
//
//                if (is_string($item['resource'])) {
//                    $item['resource'] = $container->get($item[0]);
//                }
//            }
//
//            return $container
//                ->get(MiddlewareFactory::class)
//                ->pipeline(
//                    new ResourceFindMiddleware($options['parent_resources'], $container->get(NotFoundHandler::class)),
//                    $handler
//                );
//        }

        return $handler;
    }
}
