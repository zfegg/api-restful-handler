<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\Exception\NotAcceptableHttpException;
use Zfegg\PsrMvc\Exception\NotFoundHttpException;
use Zfegg\PsrMvc\FormatMatcher;

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
        private FormatMatcher $formatMatcher,
        private SerializerInterface $serializer,
        private ResourceInterface $resource,
        private ResponseFactoryInterface $responseFactory,
        private array $serializationContext = [],
        private string $identifierName = self::IDENTIFIER_NAME,
    ) {
    }

    private function initContext(ServerRequestInterface $request): array
    {
        $context = $request->getAttributes();
        $context['query'] = $request->getQueryParams();
        $context['format'] = $request->getAttribute('format')
            ?: $this->formatMatcher->getBestFormat($request)
            ?: $this->formatMatcher->getDefaultFormat();
        $context['contentType'] = $this->formatMatcher->getFormat($context['format'])['mime-type'][0];

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

        $format = $context['format'];
        if (! $format) {
            throw new NotAcceptableHttpException(
                sprintf(
                    'Requested format "%s" is not supported.',
                    $request->getHeaderLine('accept'),
                ),
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

        return $this->createResponse($action, $result, $format, $context)
            ->withHeader('Content-Type', $context['contentType']);
    }

    private function createResponse(
        array $action,
        mixed $result,
        string $format,
        array $context
    ): ResponseInterface {

        $response = $this->responseFactory->createResponse(self::ACTION_TO_CODE[$action[0]] ?? 200);

        if ($result !== null) {
            $data = $this->serializer->serialize(
                $result,
                $format,
                $context + $this->serializationContext
            );
            $response->getBody()->write($data);
        }

        return $response;
    }
}
