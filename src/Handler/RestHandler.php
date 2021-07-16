<?php

namespace Zfegg\ApiRestfulHandler\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\ApiRestfulHandler\Exception\RequestException;
use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\ApiRestfulHandler\Utils\FormatMatcher;
use Symfony\Component\Serializer\SerializerInterface;

class RestHandler implements RequestHandlerInterface
{

    /**
     * Request 中唯ID标识名称
     *
     * @var string
     */
    const IDENTIFIER_NAME = 'id';

    public const ACTION_TO_CODE = [
        'create' => StatusCodeInterface::STATUS_CREATED,
        'delete' => StatusCodeInterface::STATUS_NO_CONTENT,
    ];

    private $resource;

    private SerializerInterface $serializer;

    private FormatMatcher $formatMatcher;

    private ResponseFactoryInterface $responseFactory;
    private array $serializeContext;

    public function __construct(
        FormatMatcher $formatMatcher,
        SerializerInterface $serializer,
        ResourceInterface $resource,
        ResponseFactoryInterface $responseFactory,
        array $serializeContext = []
    )
    {
        $this->formatMatcher = $formatMatcher;
        $this->serializer = $serializer;
        $this->resource = $resource;
        $this->responseFactory = $responseFactory;
        $this->serializeContext = $serializeContext;
    }

    private function initContext(ServerRequestInterface $request): array
    {
        $context = $request->getAttributes() + $this->serializeContext;
        $context['query'] = $request->getQueryParams();

        if ($contentType = $request->getHeaderLine('Content-Type')) {
            [$context['format']] = $this->formatMatcher->getMimeTypeFormat($contentType);
        }

        return $context;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute(self::IDENTIFIER_NAME);
        $method = $request->getMethod();
        $data = $request->getParsedBody();
        $context = $this->initContext($request);

        $actions = [
            'collection' => [
                'DELETE' => ['deleteList', ['data' => $data]],
                'GET' => ['getList', [$context]],
                'PATCH' => ['patchList', ['data' => $data]],
                'PUT' => ['replaceList', ['data' => $data]],
            ],

            'entity' => [
                'DELETE' => ['delete', [$id, $context]],
                'GET' => ['get', [$id, $context]],
                'PATCH' => ['patch', [$id, 'data' => $data, $context]],
                'PUT' => ['update', [$id, 'data' => $data, $context]],
                'POST' => ['create', ['data' => $data, $context]],
            ],
        ];

        $type = $id === null
            ? $method == 'POST' ? 'entity' : 'collection'
            : 'entity';
        $context['api_resource'] = $type;

        if (! isset($actions[$type][$method])) {
            throw new RequestException(
                'Method not allowed',
                405
            );
        }

        [$format, $contentType] = $this->formatMatcher->getFormat($request) ?: [null, null];
        if (! $format) {
            throw new RequestException(
                sprintf(
                    'Requested format "%s" is not supported.',
                    $request->getHeaderLine('accept'),
                ),
                406
            );
        }

        $action = $actions[$type][$method];

        if (in_array($action[0], ['get', 'patch', 'update', 'delete'])) {
            if (!$entity = $this->resource->get($id, $context)) {
                throw new RequestException(
                    'Entity not found.',
                    404
                );
            }
            $context['entity'] = $entity;
        } else if ($parentResource = $this->resource->getParent()) {
            $parentContext = $context;
            $parentContext['api_resource'] = 'entity';
            $parentEntity = $parentResource->get($context[$this->resource->getParentContextKey()], $parentContext);
            if (!$parentEntity) {
                throw new RequestException(
                    'Entity not found.',
                    404
                );
            }
            $context['parent_entity'] = $parentEntity;
        }

        if ($action[0] == 'get') {
            $result = $entity;
        } else {
            $result = call_user_func_array([$this->resource, $action[0]], array_values($action[1]));
        }

        return $this->createResponse($request, $action, $result, $format, $context)
            ->withHeader('Content-Type', $contentType);
    }

    private function createResponse(
        ServerRequestInterface $request,
        array $action,
        $result,
        string $format,
        array $context): ResponseInterface
    {

        $response = $this->responseFactory->createResponse(self::ACTION_TO_CODE[$action[0]] ?? 200);

        if ($result !== null) {
            $response->getBody()->write($this->serializer->serialize($result, $format, $context));
        }

        return $response;
    }
}
