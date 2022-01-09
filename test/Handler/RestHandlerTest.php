<?php

namespace ZfeggTest\ApiRestfulHandler\Handler;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ApiRestfulHandler\Handler\RestHandler;
use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\FormatMatcher;
use ZfeggTest\ApiRestfulHandler\AbstractTestCase;

class RestHandlerTest extends AbstractTestCase
{

    public function testHandle()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('{}');

        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getList')->willReturn([]);
        $responseFactory = new ResponseFactory();

        $handler = new RestHandler(
            new FormatMatcher(['json', 'csv']),
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

    public function rest()
    {
        return [
            ['GET', '/tests',],
            ['DELETE', '/tests',],
            ['POST', '/tests',],
            ['PUT', '/tests',],
            ['PATCH', '/tests',],
            ['DELETE', '/tests',],
            ['PUT', '/tests/123', ['id' => 123]],
            ['PATCH', '/tests/123', ['id' => 123]],
            ['DELETE', '/tests/123', ['id' => 123]],
//            ['GET', '/tests/123', ['id' => 123]],
        ];
    }

    /**
     * @dataProvider rest
     */
    public function testCurd(string $method, string $path, array $attrs = [])
    {
        /** @var RestHandler $handler */
        $handler = $this->container->get('demo.rest');

        $request = (new ServerRequestFactory)->createServerRequest($method, $path);

        foreach ($attrs as $attr => $val) {
            $request = $request->withAttribute($attr, $val);
        }

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(405);
        $handler->handle($request);
    }
}
