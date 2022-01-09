<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Factory;

use Psr\Container\ContainerInterface;
use Zfegg\ApiRestfulHandler\ParamResolver\ResourceResolver;

class ResourceResolverFactory
{
    public function __invoke(ContainerInterface $container): ResourceResolver
    {
        return new ResourceResolver($container);
    }
}
