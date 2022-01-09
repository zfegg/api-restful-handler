<?php

declare(strict_types = 1);

namespace ZfeggTest\ApiRestfulHandler\Resource;

use Zfegg\ApiRestfulHandler\Resource\ResourceInterface;
use Zfegg\ApiRestfulHandler\Resource\ResourceNotAllowedTrait;

class DemoResource implements ResourceInterface
{
    use ResourceNotAllowedTrait;

    /**
     * @inheritdoc
     */
    public function get($id, array $context = [])
    {
        return ['id' => $id];
    }
}
