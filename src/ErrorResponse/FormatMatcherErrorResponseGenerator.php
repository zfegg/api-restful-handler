<?php


namespace Zfegg\ApiRestfulHandler\ErrorResponse;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zfegg\ApiRestfulHandler\Utils\FormatMatcher;
use Throwable;

class FormatMatcherErrorResponseGenerator
{

    private array $responseGenerators;
    /**
     * @var FormatMatcher
     */
    private FormatMatcher $formatMatcher;

    /**
     * ErrorResponseStack constructor.
     * @param FormatMatcher $formatMatcher
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
    ) : ResponseInterface {

        [$format, ] = $this->formatMatcher->getFormat($request) ?: [null, null];

        $generator = $this->responseGenerators[$format] ?? $this->responseGenerators['default'];

        return $generator($e, $request, $response);
    }
}
