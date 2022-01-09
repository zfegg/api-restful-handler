<?php

namespace Zfegg\ApiRestfulHandler\Attribute;

use Attribute;
use Zfegg\PsrMvc\Attribute\ParamResolverAttributeInterface;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromResource implements ParamResolverAttributeInterface
{
    public function __construct(
        public string $resource,
        public string $identifier = 'id',
        public array $context = []
    )
    {
    }
}