<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\ErrorResponse;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zfegg\PsrMvc\FormatMatcher;

class FormatMatcherErrorResponseGenerator
{

    private array $responseGenerators;
    private FormatMatcher $formatMatcher;

    /**
     * ErrorResponseStack constructor.
     * @param array<string, callable> $responseGenerators
     */
    public function __construct(FormatMatcher $formatMatcher, array $responseGenerators)
    {
        if (! isset($responseGenerators['default'])) {
            throw new \InvalidArgumentException('No default response generator found.');
        }
        $this->responseGenerators = $responseGenerators;
        $this->formatMatcher = $formatMatcher;
    }

    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {

        $format = $this->formatMatcher->getBestFormat($request) ?: $this->formatMatcher->getDefaultFormat();

        $generator = $this->responseGenerators[$format] ?? $this->responseGenerators['default'];

        return $generator($e, $request, $response);
    }
}
