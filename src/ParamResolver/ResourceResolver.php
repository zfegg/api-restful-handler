<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\ParamResolver;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionParameter;
use Zfegg\PsrMvc\ParamResolver\ParamResolverInterface;

class ResourceResolver implements ParamResolverInterface
{
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    public function resolve(object $attr, ReflectionParameter $parameter): callable
    {
        /** @var \Zfegg\ApiRestfulHandler\Attribute\FromResource $attr */

        /** @var \Zfegg\ApiRestfulHandler\Resource\ResourceInterface $resource */
        $resource = $this->container->get($attr->resource);

        return static fn(ServerRequestInterface $request)
            => $resource->get($request->getAttribute($attr->identifier), $attr->context);
    }
}