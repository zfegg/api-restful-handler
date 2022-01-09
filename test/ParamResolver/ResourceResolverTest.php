<?php

namespace ZfeggTest\ApiRestfulHandler\ParamResolver;

use Zfegg\ApiRestfulHandler\Attribute\FromResource;
use Zfegg\ExpressiveTest\MockRequestFactory;
use Zfegg\PsrMvc\CallbackHandlerFactory;
use ZfeggTest\ApiRestfulHandler\AbstractTestCase;
use ZfeggTest\ApiRestfulHandler\Resource\DemoResource;

class ResourceResolverTest extends AbstractTestCase
{
    public function testResolve()
    {
        /** @var CallbackHandlerFactory $factory */
        $factory = $this->container->get(CallbackHandlerFactory::class);
        $handler = $factory->create([$this, 'demo']);

        $request = MockRequestFactory::create()->withAttribute('id', 123);

        $handler->handle($request);
    }

    public function demo(
        #[FromResource(DemoResource::class)]
        array $entity
    )
    {
        $this->assertEquals(['id' => 123], $entity);
    }
}
