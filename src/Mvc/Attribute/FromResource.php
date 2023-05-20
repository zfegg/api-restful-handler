<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Mvc\Attribute;

use Attribute;
use Zfegg\PsrMvc\Attribute\ParamResolverAttributeInterface;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromResource implements ParamResolverAttributeInterface
{
    public function __construct(
        public string $resource,
        public string $identifier = 'id',
        public array $context = [],
        public bool $nullable = false,
    ) {
    }
}
