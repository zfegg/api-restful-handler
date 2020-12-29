<?php


namespace Zfegg\ApiRestfulHandler\Factory;


use Negotiation\Negotiator;
use Psr\Container\ContainerInterface;
use Zfegg\ApiRestfulHandler\ErrorResponse\FormatMatcherErrorResponseGenerator;
use Zfegg\ApiRestfulHandler\Utils\FormatMatcher;

class FormatMatcherErrorResponseGeneratorFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')[FormatMatcherErrorResponseGenerator::class] ?? [];

        $formatMatcher = new FormatMatcher(
            $container->get(Negotiator::class),
            $config['error_formats'],
        );

        $generators = [];
        foreach ($config['generators'] as $format => $generator) {
            $generators[$format] = $container->get($generator);
        }

        return new FormatMatcherErrorResponseGenerator($formatMatcher, $generators);
    }
}
