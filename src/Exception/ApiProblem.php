<?php

namespace Zfegg\ApiRestfulHandler\Exception;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class ApiProblem extends \RuntimeException implements ProblemDetailsExceptionInterface
{
    use CommonProblemDetailsExceptionTrait;

    public function __construct(
        int $status,
        string $detail,
        string $type = '',
        string $title = '',
        array $additional = []
    ) {
        parent::__construct($detail, $status);
        $this->status = $status;
        $this->detail = $detail;
        $this->title = $title;

        if (null !== $type) {
            $this->type = $type;
        }

        $this->additional = $additional;
    }
}
