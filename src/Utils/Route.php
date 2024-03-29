<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Utils;

use Zfegg\PsrMvc\Routing\Group;

class Route
{

    public static function restRoute(Group $group, ?string $restName = null): callable
    {
        return function (
            string $resource,
            array $options = [],
            array $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE']
        ) use (
            $group,
            $restName
) {
            return $group->route(
                "/{$resource}[/{id}]",
                $restName ? sprintf($restName, $resource) : ($group->getName() . $resource),
                $methods,
                $resource,
                $options
            );
        };
    }
}
