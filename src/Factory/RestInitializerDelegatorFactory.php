<?php


namespace Zfegg\ApiRestfulHandler\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;

class RestInitializerDelegatorFactory implements DelegatorFactoryInterface
{

    /**
     * A factory that creates delegates of a given service
     *
     * @param  ContainerInterface $container
     * @param  string             $name
     * @param  callable           $callback
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(
        ContainerInterface $container,
        $name,
        callable $callback,
        array $options = null
    ) {
        // TODO: Implement __invoke() method.
    }
}
