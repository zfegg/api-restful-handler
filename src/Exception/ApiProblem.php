<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Exception;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Zfegg\PsrMvc\Exception\HttpException;

class ApiProblem extends HttpException implements ProblemDetailsExceptionInterface
{
    use CommonProblemDetailsExceptionTrait;

    public function __construct(
        int $status,
        string $detail,
        string $type = '',
        string $title = '',
        array $additional = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($status, $detail, $previous);
        $this->status = $status;
        $this->detail = $detail;
        $this->title = $title;

        if (null !== $type) {
            $this->type = $type;
        }

        $this->additional = $additional;
    }
}
