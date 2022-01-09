<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Attribute;

use Attribute;
use Zfegg\PsrMvc\Attribute\ParamResolverAttributeInterface;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromResource implements ParamResolverAttributeInterface
{
    public string $resource;
    public string $identifier = 'id';
    public array $context = [];

    public function __construct(
        string $resource,
        string $identifier = 'id',
        array $context = []
    ) {
        $this->context = $context;
        $this->identifier = $identifier;
        $this->resource = $resource;
    }
}
