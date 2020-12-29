<?php


namespace Zfegg\ApiRestfulHandler\Factory;


use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Zfegg\ApiSerializerExt\Basic\ArrayNormalizer;
use Zfegg\ApiSerializerExt\Basic\CollectionNormalizer;

class SerializerFactory
{

    public function __invoke(ContainerInterface $container)
    {

        return $serializer = new Serializer(
            [
                new ArrayNormalizer(),
                new CollectionNormalizer(),
            ],
            [
                new JsonEncoder(),
            ]
        );
    }
}