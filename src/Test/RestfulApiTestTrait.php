<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Test;

/**
 * Trait RestfulApiTestTrait
 *
 * @mixin \Zfegg\ExpressiveTest\AbstractActionTestCase
 * @property string $path
 */
trait RestfulApiTestTrait
{
    public function apiCurd(array $postBody, array $putBody, array $patchBody, array $query = []): void
    {
        $id = $this->apiCreate($postBody);
        $this->apiUpdate($id, $putBody);
        $this->apiPatch($id, $patchBody);
        $this->apiGetList($query);
        $this->apiGet($id);
        $this->apiDelete($id);
    }

    /**
     * @param int|string $id
     */
    public function apiDelete($id): void
    {
        $response = $this->delete($this->path . '/' . $id);
        $response->assertNoContent();
    }

    /**
     * @param int|string $id
     */
    public function apiGet($id): void
    {
        $response = $this->get($this->path . '/' . $id);
        $response->assertOk();
    }

    /**
     * @param array $query
     */
    public function apiGetList(array $query = []): void
    {
        $response = $this->get($this->path . '?' . http_build_query($query));
        $response->assertOk()
            ->assertJsonStructure(['data']);
    }

    /**
     * @return int|string
     */
    public function apiCreate(array $body, int $status = 201, string $primaryKey = 'id')
    {
        $response = $this->postJson($this->path, $body);
        $response->assertStatus($status);

        return $response->json()[$primaryKey];
    }

    /**
     * @param int|string $id
     */
    public function apiUpdate($id, array $params): void
    {
        $this->putJson($this->path . '/' . $id, $params)->assertOk();
    }

    /**
     * @param int|string $id
     */
    public function apiPatch($id, array $params): void
    {
        $this->patchJson($this->path . '/' . $id, $params)->assertOk();
    }
}
