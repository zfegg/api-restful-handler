<?php

namespace ZfeggTest\ApiRestfulHandler\Utils;

use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface;
use Zfegg\ApiRestfulHandler\Utils\FormatMatcher;
use PHPUnit\Framework\TestCase;

class FormatMatcherTest extends TestCase
{
    public function headers()
    {
        return [
            ['application/ld+json', null],
            ['*/*', 'jsonld'],
            ['*/*', null],
        ];
    }

    /**
     *
     * @dataProvider headers()
     */
    public function testGetFormat(string $accept, ?string $format)
    {
        $matcher = new FormatMatcher(
            new Negotiator,
            [
                'jsonld',
                'json',
            ]
        );

        $req = $this->createMock(ServerRequestInterface::class);
        $req->method('getHeaderLine')->willReturn($accept);
        $req->method('getAttribute')->willReturn($format);

        $res = $matcher->getFormat($req);

        $this->assertEquals('jsonld', $res[0]);
        $this->assertEquals('application/ld+json', $res[1]);
    }
}
