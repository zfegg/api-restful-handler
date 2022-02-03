<?php

declare(strict_types = 1);

namespace ZfeggTest\ApiRestfulHandler\Resource;

use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\ApiRestfulHandler\Resource\ResourceNotAllowedTrait;

class NotAllowedResource implements ResourceInterface
{
    use ResourceNotAllowedTrait;


    /**
     * @inheritdoc
     */
    public function get(int|string $id, array $context = []): array|object|null
    {
        return ['id' => $id];
    }
}
