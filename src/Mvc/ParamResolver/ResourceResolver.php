<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Mvc\ParamResolver;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionParameter;
use Zfegg\PsrMvc\Exception\NotFoundHttpException;
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
        /** @var \Zfegg\ApiRestfulHandler\Mvc\Attribute\FromResource $attr */

        /** @var \Zfegg\ApiRestfulHandler\ResourceInterface $resource */
        $resource = $this->container->get($attr->resource);

        return static function (ServerRequestInterface $request) use ($resource, $attr) {
            $entity = $resource->get(
                $request->getAttribute($attr->identifier),
                $attr->context + $request->getAttributes()
            );
            if (! $entity && ! $attr->nullable) {
                throw new NotFoundHttpException("Entity not found");
            }
            return $entity;
        };
    }
}
