<?php


namespace Zfegg\ApiRestfulHandler\Utils;


use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface;
use Zfegg\ApiRestfulHandler\Exception\RequestException;

class FormatMatcher
{

    public const MIME_TYPES = [
        'csv' => ['text/csv'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'js' => ['application/javascript'],
        'json' => ['application/json'],
        'jsonld' => ['application/ld+json'],
        'jsonhal' =>  ['application/hal+json'],
        'jsonapi' =>  ['application/vnd.api+json'],
        'jsonproblem' =>  ['application/problem+json'],
        'html' => ['text/html', 'application/xhtml+xml'],
        'txt' => ['text/plain'],
        'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'yaml' => ['text/yaml'],
        'yml' => ['text/yaml'],
    ];

    /**
     * @var array<string, string[]>
     */
    private array $formats;

    private Negotiator $negotiator;

    /**
     * @var array<string, string>
     */
    private array $mimeTypes;

    /**
     * @param Negotiator $negotiator
     * @param array<string, string[]|string> $formats
     */
    public function __construct(Negotiator $negotiator, array $formats)
    {
        $normalizedFormats = [];
        foreach ($formats as $format => $mimeTypes) {
            if (is_int($format)) {
                $format = $mimeTypes;
                $mimeTypes = self::fromExtension($format);
            }
            $mimeTypes = (array) $mimeTypes;

            $normalizedFormats[$format] = $mimeTypes;
            foreach ($mimeTypes as $mimeType) {
                $this->mimeTypes[$mimeType] = $format;
            }
        }

        $this->formats = $normalizedFormats;
        $this->negotiator = $negotiator;
    }

    /**
     * Gets the format associated with the mime type.
     *
     */
    public function getFormat(ServerRequestInterface $request): ?array
    {
        // Empty strings must be converted to null because the Symfony router doesn't support parameter typing before 3.2 (_format)
        if (null === $routeFormat = $request->getAttribute('format')) {
            $mimeTypes = array_keys($this->mimeTypes);
        } elseif (!isset($this->formats[$routeFormat])) {
            throw new RequestException(sprintf('Format "%s" is not supported', $routeFormat), 404);
        } else {
            $mimeTypes = $this->formats[$routeFormat];
        }

        /** @var \Negotiation\Accept $accept */
        $accept = $this->negotiator->getBest(
            $request->getHeaderLine('Accept') ?: '*/*',
            $mimeTypes
        );

        if (! $accept) {
            return null;
        }

        $mimeType = $accept->getType();

        return $this->getMimeTypeFormat($mimeType);
    }

    public function getMimeTypeFormat(string $mimeType): ?array
    {
        $canonicalMimeType = null;
        $pos = strpos($mimeType, ';');
        if (false !== $pos) {
            $canonicalMimeType = trim(substr($mimeType, 0, $pos));
        }

        $format = $this->mimeTypes[$canonicalMimeType ?: $mimeType] ?? null;
        return $format ? [$format, $mimeType] : null;
    }

    public static function fromExtension(string $extension): ?array
    {
        $extension = strtolower($extension);

        return self::MIME_TYPES[$extension] ?? null;
    }
}
