<?php

namespace ImportData\Options\Factory;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ImportData\Options\ModuleOptions;

class ModuleOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $options = $config[''];
        return new ModuleOptions($options);
    }
}
