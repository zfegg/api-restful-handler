<?php


namespace Zfegg\ApiRestfulHandler\Utils;


use Negotiation\Negotiator;
use Psr\Http\Message\RequestInterface;

class FormatMatcher
{

    /**
     * @var array<string, string[]>
     */
    private $formats;

    /** @var Negotiator  */
    private $negotiator;

    /**
     * @param Negotiator $negotiator
     * @param array<string, string[]|string> $formats
     */
    public function __construct(Negotiator $negotiator, array $formats)
    {
        $normalizedFormats = [];
        foreach ($formats as $format => $mimeTypes) {
            $normalizedFormats[$format] = (array) $mimeTypes;
        }
        $this->formats = $normalizedFormats;
        $this->negotiator = $negotiator;
    }

    /**
     * Gets the format associated with the mime type.
     *
     */
    public function getFormat(RequestInterface $request): ?array
    {
        /** @var \Negotiation\Accept $accept */
        $accept = $this->negotiator->getBest(
            $request->getHeaderLine('Accept') ?: '*/*',
            array_merge(...array_values($this->formats))
        );

        if (! $accept) {
            return null;
        }

        $mimeType = $accept->getType();

        $canonicalMimeType = null;
        $pos = strpos($mimeType, ';');
        if (false !== $pos) {
            $canonicalMimeType = trim(substr($mimeType, 0, $pos));
        }

        foreach ($this->formats as $format => $mimeTypes) {
            if (\in_array($mimeType, $mimeTypes, true)) {
                return [$format, $mimeType];
            }
            if (null !== $canonicalMimeType && \in_array($canonicalMimeType, $mimeTypes, true)) {
                return [$format, $canonicalMimeType];
            }
        }

        return null;
    }
}
