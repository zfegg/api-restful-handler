<?php


namespace SimpleRestTest;


use Zfegg\ExpressiveTest\AbstractActionTestCase;

class DemoTest extends AbstractActionTestCase
{

    public function testMime()
    {
        $mimes = new \Mimey\MimeTypes;

        $rs = $mimes->getMimeType('jsonld'); // application/json

        $rs2 = $mimes->getExtension('application/json'); // json
        $this->assertTrue(true);
    }
}