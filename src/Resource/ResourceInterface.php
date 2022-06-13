<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Resource;

interface ResourceInterface
{

    /**
     * Create a record in the resource
     *
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function create(object|array $data, array $context = []): object|array;

    /**
     * Update (replace) an existing record
     *
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function update(int|string $id, object|array $data, array $context = []): object|array;

    /**
     * Update (replace) an existing collection of records
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function replaceList(iterable $data, array $context = []): iterable;

    /**
     * Partial update of an existing record
     *
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function patch(int|string $id, object|array $data, array $context = []): object|array;

    /**
     * Delete an existing record
     */
    public function delete(int|string $id, array $context = []): void;

    /**
     * Delete an existing collection of records
     */
    public function deleteList(?iterable $data = null, array $context = []): void;

    /**
     * Fetch an existing record
     */
    public function get(int|string $id, array $context = []): array|object|null;

    /**
     * Fetch a collection of records
     *
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function getList(array $context = []): iterable;

    /**
     *
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function patchList(iterable $data, array $context = []): iterable;

    /**
     * Parent resource
     *
     */
    public function getParent(): ?ResourceInterface;

    public function getParentContextKey(): ?string;
}
