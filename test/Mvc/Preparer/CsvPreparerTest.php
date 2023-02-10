<?php

declare(strict_types = 1);

namespace ZfeggTest\ApiRestfulHandler\Mvc\Preparer;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Zfegg\ApiRestfulHandler\Mvc\Preparer\CsvPreparer;
use Zfegg\PsrMvc\FormatMatcher;

class CsvPreparerTest extends TestCase
{

    public function testPrepare(): void
    {
        $matcher = new FormatMatcher(['csv']);
        $responseFactory = new ResponseFactory();
        $serializer = new Serializer();
        $csvPreparer = new CsvPreparer($matcher, $serializer, $responseFactory);

        $req = new ServerRequest();
        $req = $req->withAttribute('format', 'csv');

        $data = (function () {
            $data = [];
            for ($i = 1; $i < 5; $i++) {
                $data[] = ['id' => $i, 'a' => ['a1' => 1, 'a2' => 4], 'b' => 2];
            }
            return $data;
        })();

//        $csv = new CsvEncoder();
//        $result = $csv->encode($data, 'csv', ['csv_headers' => ['A' => 'a.a2', 'id']]);

        self::assertTrue($csvPreparer->supportsPreparation($req, $data));

        $bodyText = <<<EOT
1,1,4,2
2,1,4,2
3,1,4,2
4,1,4,2

EOT;
        // Test default
        $response = $csvPreparer->prepare($req, $data);
        $text = $response->getBody()->getContents();
        self::assertEquals(<<<EOT
id,a.a1,a.a2,b
$bodyText
EOT, $text);

        // Test no headers
        $response = $csvPreparer->prepare($req, $data, ['no_headers' => true]);
        $text = $response->getBody()->getContents();
        self::assertEquals($bodyText, $text);

        // Test header alias
        $response = $csvPreparer->prepare($req, $data, ['csv_headers' => ['b', 'id', 'A' => 'a.a2']]);
        $text = $response->getBody()->getContents();
        self::assertEquals(<<<EOT
b,id,A,a.a1
2,1,4,1
2,2,4,1
2,3,4,1
2,4,4,1

EOT, $text);


        // Test response read.
        $response = $csvPreparer->prepare($req, $data, ['no_headers' => true, 'output_utf8_bom' => true,]);
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        self::assertTrue($body->isReadable());

        $lines = str_split($bodyText, 8);
        array_unshift($lines, CsvPreparer::UTF8_BOM);
        $i = 0;
        while (! $body->eof()) {
            $buf = $body->read(8196);
            self::assertEquals($lines[$i], $buf);
            $i++;
        }
    }
}
