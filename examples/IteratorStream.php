<?php


use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Symfony\Component\Serializer\Serializer;
use Zfegg\ApiRestfulHandler\Mvc\Preparer\CsvPreparer;
use Zfegg\PsrMvc\FormatMatcher;
use Zfegg\PsrMvc\Http\Emitter\SapiIteratorStreamEmitter;

include __DIR__ . '/../vendor/autoload.php';


$data = (function () {
    for ($i = 1; $i < 5; $i++) {
        sleep(1);
        yield ['id' => $i, 'a' => ['a1' => 1, 'a2' => 4], 'b' => 2, 't' => date('H:i:s')];
    }
})();


$matcher = new FormatMatcher(['csv']);
$responseFactory = new ResponseFactory();
$serializer = new Serializer();
$csvPreparer = new CsvPreparer($matcher, $serializer, $responseFactory);


$req = new ServerRequest();
$req = $req->withAttribute('format', 'csv');

$response = $csvPreparer->prepare($req, $data);
//echo date('H:i:s') . "\n";
//
//$rs = $stream->getContents();
//echo $rs;
//exit;
//while (! $stream->eof()) {
//    echo date('H:i:s') . ">> \n";
//    echo $stream->read(0);
//    echo date('H:i:s') . " <<\n";
//}
//exit;

//$response = new \Laminas\Diactoros\Response($stream);

$emitter = new SapiIteratorStreamEmitter();
$emitter->emit($response);