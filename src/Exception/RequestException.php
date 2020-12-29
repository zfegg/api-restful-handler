<?php


namespace Zfegg\ApiRestfulHandler\Exception;


use Throwable;

class RequestException extends \RuntimeException implements RequestExceptionInterface
{
    public function __construct(int $code, string $message, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}