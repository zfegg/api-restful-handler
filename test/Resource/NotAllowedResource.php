<?php

namespace ZfeggTest\ApiRestfulHandler\Resource;

use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\ApiRestfulHandler\Resource\ResourceNotAllowedTrait;

class NotAllowedResource implements ResourceInterface
{
    use ResourceNotAllowedTrait;

    public function get($id, array $context = [])
    {
        return ['id' => $id];
    }
}