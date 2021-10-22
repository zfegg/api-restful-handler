<?php


namespace Zfegg\ApiRestfulHandler\Factory;


use Psr\Container\ContainerInterface;
use Zfegg\ApiRestfulHandler\Middleware\ResourceFindMiddleware;

class ResourceFindMiddlewareFactory
{
    public function __invoke(ContainerInterface $container, string $className, ?array $options = null)
    {
        return new ResourceFindMiddleware(
            $container->get($options['resource']),
            $options['attr'] ?? 'entity',
        );
    }

}