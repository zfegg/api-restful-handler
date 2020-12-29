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

    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var FormatMatcher
     */
    private FormatMatcher $formatMatcher;

    /**
     * @var ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        FormatMatcher $formatMatcher,
        SerializerInterface $serializer,
        ResourceInterface $resource,
        ResponseFactoryInterface $responseFactory
    )
    {
        $this->formatMatcher = $formatMatcher;
        $this->serializer = $serializer;
        $this->resource = $resource;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $params = $routeResult->getMatchedParams();
        $method = $request->getMethod();
        $id = $params[self::IDENTIFIER_NAME] ?? null;
        $data = $request->getParsedBody();
        $context = $request->getAttributes();
        $context['query'] = $request->getQueryParams();

        $actions = [
            'collection' => [
                'DELETE' => ['deleteList', ['data' => $data]],
                'GET' => ['getList', [$context]],
                'PATCH' => ['patchList', ['data' => $data]],
                'POST' => ['create', ['data' => $data, $context]],
                'PUT' => ['replaceList', ['data' => $data]],
            ],

            'entity' => [
                'DELETE' => ['delete', [$id, $context]],
                'GET' => ['get', [$id, $context]],
                'PATCH' => ['patch', [$id, 'data' => $data, $context]],
                'PUT' => ['update', [$id, 'data' => $data, $context]],
            ],
        ];
        $type = $id === null ? 'collection' : 'entity';

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

        $result = call_user_func_array([$this->resource, $action[0]], $action[1]);

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
        if ($action == 'get' && ! $result) {
            throw new RequestException(
                'Entity not found.',
                404
            );
        }

        $response = $this->responseFactory->createResponse(self::ACTION_TO_CODE[$action[0]] ?? 200);

        if ($result) {
            $response->getBody()->write($this->serializer->serialize($result, $format, $context));
        }

        return $response;
    }
}
