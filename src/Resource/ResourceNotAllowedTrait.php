<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Resource;

use Zfegg\PsrMvc\Exception\HttpException;

trait ResourceNotAllowedTrait
{
    /**
     * @inheritDoc
     */
    public function create(object|array $data, array $context = []): object|array
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function update(int|string $id, object|array $data, array $context = []): object|array
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function replaceList($data, array $context = []): iterable
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function patch(int|string $id, object|array $data, array $context = []): object|array
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function delete(int|string $id, array $context = []): void
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function deleteList(?iterable $data = null, array $context = []): void
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function get(int|string $id, array $context = []): array|object|null
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function getList(array $context = []): iterable
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function patchList($data, array $context = []): iterable
    {
        throw new HttpException(405, 'Method Not Allowed');
    }

    public function getParent(): ?ResourceInterface
    {
        return null;
    }

    public function getParentContextKey(): ?string
    {
        return null;
    }
}
