<?php

declare(strict_types = 1);

namespace ZfeggTest\ApiRestfulHandler;

use Zfegg\ApiRestfulHandler\ResourceInterface;
use Zfegg\ApiRestfulHandler\ResourceNotAllowedTrait;

class DemoResource implements ResourceInterface
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
