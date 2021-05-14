<?php

namespace Zfegg\ApiRestfulHandler\Resource;

use Zfegg\ApiRestfulHandler\Exception\ApiProblem;

interface ResourceInterface
{

    /**
     * Create a record in the resource
     *
     * @param array|object $data
     * @param array $context
     * @return array|object
     * @throws ApiProblem
     */
    public function create($data, array $context = []);

    /**
     * Update (replace) an existing record
     *
     * @param  string|int $id
     * @param  array|object $data
     *
     * @return array|object
     * @throws ApiProblem
     */
    public function update($id, $data, array $context = []);

    /**
     * Update (replace) an existing collection of records
     *
     * @param  array $data
     *
     * @return iterable
     * @throws ApiProblem
     */
    public function replaceList($data, array $context = []): iterable;

    /**
     * Partial update of an existing record
     *
     * @param  string|int $id
     * @param  array|object $data
     *
     * @return array|object
     * @throws ApiProblem
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
     *
     * @param  null|array $data
     */
    public function deleteList($data = null, array $context = []): void;

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
     * @throws ApiProblem
     */
    public function getList(array $context = []): iterable;

    /**
     * @param $data
     *
     * @throws ApiProblem
     */
    public function patchList($data, array $context = []): iterable;

    /**
     * Parent resource
     *
     * @return ResourceInterface|null
     */
    public function getParent(): ?ResourceInterface;

    public function getParentContextKey(): ?string;
}
