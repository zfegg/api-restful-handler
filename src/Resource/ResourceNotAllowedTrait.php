<?php


namespace Zfegg\ApiRestfulHandler\Resource;

use Zfegg\ApiRestfulHandler\Exception\ApiProblem;

trait ResourceNotAllowedTrait
{
    /**
     * Create a record in the resource
     *
     * @param  array|object $data
     *
     * @return array|object
     * @throws ApiProblem
     */
    public function create($data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * Update (replace) an existing record
     *
     * @param  string|int   $id
     * @param  array|object $data
     *
     * @return array|object
     * @throws ApiProblem
     */
    public function update($id, $data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * Update (replace) an existing collection of records
     *
     * @param  array $data
     *
     * @return array|object
     * @throws ApiProblem
     */
    public function replaceList($data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * Partial update of an existing record
     *
     * @param  string|int   $id
     * @param  array|object $data
     *
     * @return array|object
     * @throws ApiProblem
     */
    public function patch($id, $data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * Delete an existing record
     *
     * @param  string|int $id
     *
     * @return bool
     * @throws ApiProblem
     */
    public function delete($id, array $context = []): void
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * Delete an existing collection of records
     *
     * @param  null|array $data
     *
     * @return bool
     * @throws ApiProblem
     */
    public function deleteList($data = null, array $context = []): bool
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * Fetch an existing record
     *
     * @param  string|int $id
     *
     * @return false|array|object
     * @throws ApiProblem
     */
    public function get($id, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * Fetch a collection of records
     *
     * @throws ApiProblem
     */
    public function getList(array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @param $data
     *
     * @throws ApiProblem
     */
    public function patchList($data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }
}
