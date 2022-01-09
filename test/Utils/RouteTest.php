<?php

declare(strict_types = 1);

namespace ZfeggTest\ApiRestfulHandler\Utils;

use Zfegg\ApiRestfulHandler\Utils\Route;
use PHPUnit\Framework\TestCase;
use Zfegg\PsrMvc\Routing\Group;

class RouteTest extends TestCase
{

    public function testRestRoute(): void
    {
        $group = new Group('/api', [], 'api.');
        $rest = Route::restRoute($group);
        $rest('tests');

        $this->assertEquals(
            [
                'api.tests' => [
                    'path'            => '/api/tests[/{id}]',
                    'middleware'      => ['api.tests',],
                    'allowed_methods' => [
                        'GET',
                        'POST',
                        'PATCH',
                        'PUT',
                        'DELETE',
                    ],
                    'name'            => 'api.tests',
                    'options'         => [],
                ],
            ],
            $group->getRoutes()
        );
    }
}
