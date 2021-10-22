<?php

namespace ZfeggTest\ApiRestfulHandler\Handler;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Handler\RestHandler;
use PHPUnit\Framework\TestCase;
use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\ApiRestfulHandler\Utils\FormatMatcher;

class RestHandlerTest extends TestCase
{

    public function testHandle()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('{}');

        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getList')->willReturn([]);
        $responseFactory = new ResponseFactory();

        $handler = new RestHandler(
            new FormatMatcher(new Negotiator(), ['json', 'csv']),
            $serializer,
            $resource,
            $responseFactory,
            []
        );

        $request = (new ServerRequestFactory)->createServerRequest('GET', '/foo');
        $response = $handler->handle($request);

        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('{}', (string) $response->getBody());
    }
}
