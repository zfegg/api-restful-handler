<?php

declare(strict_types = 1);

namespace Zfegg\ApiRestfulHandler\Mvc\Preparer;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use TypeError;
use Zfegg\PsrMvc\FormatMatcher;
use Zfegg\PsrMvc\Http\IteratorStream;
use Zfegg\PsrMvc\Preparer\ResultPreparableInterface;

class CsvPreparer implements ResultPreparableInterface
{

    public const FORMAT = 'csv';
    public const DELIMITER_KEY = 'csv_delimiter';
    public const ENCLOSURE_KEY = 'csv_enclosure';
    public const ESCAPE_CHAR_KEY = 'csv_escape_char';
    public const KEY_SEPARATOR_KEY = 'csv_key_separator';
    public const HEADERS_KEY = 'csv_headers';
    public const ESCAPE_FORMULAS_KEY = 'csv_escape_formulas';
    public const AS_COLLECTION_KEY = 'as_collection';
    public const NO_HEADERS_KEY = 'no_headers';
    public const END_OF_LINE = 'csv_end_of_line';
    public const OUTPUT_UTF8_BOM_KEY = 'output_utf8_bom';

    public const UTF8_BOM = "\xEF\xBB\xBF";

    private const FORMULAS_START_CHARACTERS = ['=', '-', '+', '@', "\t", "\r"];

    private array $defaultContext = [
        self::DELIMITER_KEY => ',',
        self::ENCLOSURE_KEY => '"',
        self::ESCAPE_CHAR_KEY => '',
        self::END_OF_LINE => "\n",
        self::ESCAPE_FORMULAS_KEY => false,
        self::HEADERS_KEY => [],
        self::KEY_SEPARATOR_KEY => '.',
        self::NO_HEADERS_KEY => false,
        self::AS_COLLECTION_KEY => true,
        self::OUTPUT_UTF8_BOM_KEY => false,
    ];


    public function __construct(
        private FormatMatcher $matcher,
        private NormalizerInterface $serializer,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function supportsPreparation(ServerRequestInterface $request, mixed $result, array $options = []): bool
    {
        $format = $request->getAttribute('format')
            ?: $this->matcher->getBestFormat($request)
                ?: $this->matcher->getDefaultFormat();

        return $format == self::FORMAT && is_iterable($result);
    }

    public function prepare(ServerRequestInterface $request, mixed $result, array $context = []): ResponseInterface
    {
        $format = self::FORMAT;
        $mimeType = $this->matcher->getFormat($format)['mime-type'][0];
        $response = $this->responseFactory->createResponse($context['status'] ?? 200);
        $response = $response->withHeader('Content-Type', $mimeType);

        foreach ($context['headers'] ?? [] as $name => $header) {
            $response = $response->withHeader($name, $header);
        }

        $iterator = (function () use ($result, $context) {
            [
                $delimiter, $enclosure, $escapeChar,
                $keySeparator, $headers, $escapeFormulas,
                $outputBom, $noHeadersKey
            ] = $this->getCsvOptions($context);


            $i = 0;
            foreach ($result as $item) {
                $item = $this->serializer->normalize($item, 'csv', $context);

                $flattened = [];
                $this->flatten($item, $flattened, $keySeparator, '', $escapeFormulas);
                $item = $flattened;

                if ($i === 0) {
                    $headers = array_merge($headers, array_diff($this->extractHeaders($item), $headers));
                    $header = $this->firstRow($headers, $outputBom, $noHeadersKey, $delimiter, $enclosure, $escapeChar);
                    if ($header) {
                        yield $header;
                    }
                }

                $out = array_fill_keys(array_values($headers), '');
                $out = array_replace($out, $item);

                yield self::putcsv($out, $delimiter, $enclosure, $escapeChar);
                $i++;
            }
        })();

        $stream = new IteratorStream($iterator);

        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withBody($stream);
    }

    private function firstRow(
        array $headers,
        bool $outputBom,
        bool $noHeadersKey,
        string $delimiter,
        string $enclosure,
        string $escapeChar
    ): string {
        $out = "";
        if ($outputBom) {
            $out .= self::UTF8_BOM;
        }
        if ($noHeadersKey) {
            return $out;
        }

        $titles = [];
        foreach ($headers as $key => $title) {
            if (is_int($key)) {
                $titles[] = $title;
            } else {
                $titles[] = $key;
            }
        }

        return $out . self::putcsv($titles, $delimiter, $enclosure, $escapeChar);
    }

    private static function putcsv(
        array $fields,
        string $separator = ",",
        string $enclosure = "\"",
        string $escape = "\\",
        string $eol = "\n"
    ): string {
        return implode(
            $separator,
            array_map(
                function ($a) use ($enclosure, $escape, $separator) {
                    $type = gettype($a);
                    switch ($type) {
                        case 'integer':
                            return sprintf('%d', $a);
                        case 'double':
                            return rtrim(sprintf('%0.' . ini_get('precision') . 'f', $a), '0');
                        case 'boolean':
                            return ($a ? 'true' : 'false');
                        case 'NULL':
                            return '';
                        case 'string':
                            if (str_contains($a, ' ') ||
                                str_contains($a, $separator) ||
                                str_contains($a, $enclosure)
                            ) {
                                return sprintf('"%s"', str_replace(
                                    [$escape, $enclosure],
                                    [$escape . $escape, $escape . $enclosure],
                                    $a
                                ));
                            }

                            return $a;
                        default:
                            throw new TypeError("Cannot stringify type: $type");
                    }
                },
                $fields
            )
        ) . $eol;
    }

    private function getCsvOptions(array $context): array
    {
        $delimiter = $context[self::DELIMITER_KEY] ?? $this->defaultContext[self::DELIMITER_KEY];
        $enclosure = $context[self::ENCLOSURE_KEY] ?? $this->defaultContext[self::ENCLOSURE_KEY];
        $escapeChar = $context[self::ESCAPE_CHAR_KEY] ?? $this->defaultContext[self::ESCAPE_CHAR_KEY];
        $keySeparator = $context[self::KEY_SEPARATOR_KEY] ?? $this->defaultContext[self::KEY_SEPARATOR_KEY];
        $headers = $context[self::HEADERS_KEY] ?? $this->defaultContext[self::HEADERS_KEY];
        $escapeFormulas = $context[self::ESCAPE_FORMULAS_KEY] ?? $this->defaultContext[self::ESCAPE_FORMULAS_KEY];
        $outputBom = $context[self::OUTPUT_UTF8_BOM_KEY] ?? $this->defaultContext[self::OUTPUT_UTF8_BOM_KEY];
        $noHeadersKey = $context[self::NO_HEADERS_KEY] ?? $this->defaultContext[self::NO_HEADERS_KEY];

        if (! \is_array($headers)) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" context variable must be an array or null, given "%s".',
                self::HEADERS_KEY,
                get_debug_type($headers)
            ));
        }

        return [
            $delimiter,
            $enclosure,
            $escapeChar,
            $keySeparator,
            $headers,
            $escapeFormulas,
            $outputBom,
            $noHeadersKey,
        ];
    }


    /**
     * @return string[]
     */
    private function extractHeaders(array $row): array
    {
        $headers = [];
        $flippedHeaders = [];

        $previousHeader = null;

        foreach ($row as $header => $v) {
            if (isset($flippedHeaders[$header])) {
                $previousHeader = $header;
                continue;
            }

            if (null === $previousHeader) {
                $n = \count($headers);
            } else {
                $n = $flippedHeaders[$previousHeader] + 1;

                for ($j = \count($headers); $j > $n; --$j) {
                    ++$flippedHeaders[$headers[$j] = $headers[$j - 1]];
                }
            }

            $headers[$n] = $header;
            $flippedHeaders[$header] = $n;
            $previousHeader = $header;
        }

        return $headers;
    }

    private function flatten(
        iterable $array,
        array &$result,
        string $keySeparator,
        string $parentKey = '',
        bool $escapeFormulas = false
    ): void {
        foreach ($array as $key => $value) {
            if (is_iterable($value)) {
                $this->flatten($value, $result, $keySeparator, $parentKey . $key . $keySeparator, $escapeFormulas);
            } else {
                if ($escapeFormulas && \in_array(substr((string)$value, 0, 1), self::FORMULAS_START_CHARACTERS, true)) {
                    $result[$parentKey . $key] = "'" . $value;
                } else {
                    // Ensures an actual value is used when dealing with true and false
                    $result[$parentKey . $key] = false === $value ? 0 : (true === $value ? 1 : $value);
                }
            }
        }
    }
}
