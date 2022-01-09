<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Resource;


interface ResourceInterface
{

    /**
     * Create a record in the resource
     *
     * @param array|object $data
     * @param array $context
     * @return array|object
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function create($data, array $context = []);

    /**
     * Update (replace) an existing record
     *
     * @param  string|int $id
     * @param  array|object $data
     *
     * @return array|object
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function update($id, $data, array $context = []);

    /**
     * Update (replace) an existing collection of records
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function replaceList(iterable $data, array $context = []): iterable;

    /**
     * Partial update of an existing record
     *
     * @param  string|int $id
     * @param  array|object $data
     *
     * @return array|object
     * @throws \Zfegg\PsrMvc\Exception\HttpExceptionInterface
     */
    public function patch($id, $data, array $context = []);

    /**
     * Delete an existing record
     *
     * @param  string|int $id
     */
    public function delete($id, array $context = []): void;

    /**
     * Delete an existing collection of records
     */
    public function deleteList(?iterable $data = null, array $context = []): void;

    /**
     * Fetch an existing record
     *
     * @param  string|int $id
     * @return array|object|void
     */
    public function get($id, array $context = []);

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
