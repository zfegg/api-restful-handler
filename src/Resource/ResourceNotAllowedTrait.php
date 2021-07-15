<?php


namespace Zfegg\ApiRestfulHandler\Resource;

use Zfegg\ApiRestfulHandler\Exception\ApiProblem;

trait ResourceNotAllowedTrait
{
    /**
     * @inheritDoc
     */
    public function create($data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function update($id, $data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function replaceList($data, array $context = []): iterable
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function patch($id, $data, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function delete($id, array $context = []): void
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function deleteList($data = null, array $context = []): void
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function get($id, array $context = [])
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function getList(array $context = []): iterable
    {
        throw new ApiProblem(405, 'Method Not Allowed');
    }

    /**
     * @inheritDoc
     */
    public function patchList($data, array $context = []): iterable
    {
        throw new ApiProblem(405, 'Method Not Allowed');
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
