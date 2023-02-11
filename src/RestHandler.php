<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\Exception\NotFoundHttpException;
use Zfegg\PsrMvc\Preparer\ResultPreparableInterface;

class RestHandler implements RequestHandlerInterface
{
    /**
     * Request 中唯ID标识名称
     */
    const IDENTIFIER_NAME = 'id';

    public const ACTION_TO_CODE = [
        'create' => StatusCodeInterface::STATUS_CREATED,
        'delete' => StatusCodeInterface::STATUS_NO_CONTENT,
    ];

    public function __construct(
        private ResultPreparableInterface $resultPreparer,
        private ResourceInterface $resource,
        private array $serializationContext = [],
        private string $identifierName = self::IDENTIFIER_NAME,
    ) {
    }

    private function initContext(ServerRequestInterface $request): array
    {
        $context = $request->getAttributes();
        $context['query'] = $request->getQueryParams();

        return $context;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute($this->identifierName);
        $method = $request->getMethod();
        $data = $request->getParsedBody();
        $context = $this->initContext($request);

        $actions = [
            'collection' => [
                'DELETE' => ['deleteList', ['data' => $data]],
                'GET' => ['getList', []],
                'PATCH' => ['patchList', ['data' => $data]],
                'PUT' => ['replaceList', ['data' => $data]],
            ],

            'entity' => [
                'DELETE' => ['delete', [$id,]],
                'GET' => ['get', [$id,]],
                'PATCH' => ['patch', [$id, 'data' => $data,]],
                'PUT' => ['update', [$id, 'data' => $data,]],
                'POST' => ['create', ['data' => $data,]],
            ],
        ];

        $type = $id === null
            ? $method == 'POST' ? 'entity' : 'collection'
            : 'entity';
        $context['api_resource'] = $type;

        if (! isset($actions[$type][$method])) {
            throw new HttpException(
                405,
                'Method not allowed',
            );
        }

        $action = $actions[$type][$method];

        if (in_array($action[0], ['get', 'patch', 'update', 'delete'])) {
            if (! $entity = $this->resource->get($id, $context)) {
                throw new NotFoundHttpException('Entity not found.');
            }
            $context['entity'] = $entity;
        } elseif ($parentResource = $this->resource->getParent()) {
            $parentContext = $context;
            $parentContext['api_resource'] = 'entity';
            $parentEntity = $parentResource->get($context[$this->resource->getParentContextKey()], $parentContext);
            if (! $parentEntity) {
                throw new NotFoundHttpException('Entity not found.');
            }
            $context['parent_entity'] = $parentEntity;
        }

        if ($action[0] == 'get') {
            $result = $entity;
        } else {
            $action[1][] = $context;
            $result = call_user_func_array([$this->resource, $action[0]], array_values($action[1]));
        }
        if (isset(self::ACTION_TO_CODE[$action[0]])) {
            $context['status'] = self::ACTION_TO_CODE[$action[0]];
        }

        return $this->resultPreparer->prepare($request, $result, $context + $this->serializationContext);
    }
}
