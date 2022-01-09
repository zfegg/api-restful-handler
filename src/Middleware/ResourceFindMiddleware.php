<?php

namespace Zfegg\ApiRestfulHandler\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\ApiRestfulHandler\Handler\RestHandler;
use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\PsrMvc\Exception\NotFoundHttpException;

class ResourceFindMiddleware implements MiddlewareInterface
{
    private ResourceInterface $resource;

    private string $attr;

    public function __construct(ResourceInterface $resource, string $attr = 'entity')
    {
        $this->resource = $resource;
        $this->attr = $attr;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$entity = $this->resource->get($request->getAttribute(RestHandler::IDENTIFIER_NAME))) {
            throw new NotFoundHttpException('Entity not found');
        }

        $request = $request->withAttribute($this->attr, $entity);

        return $handler->handle($request);
    }
}
