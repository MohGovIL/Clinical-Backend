<?php

namespace Formhandler\View\Helper\Factory\Form;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Formhandler\Form\View\Helper\TwbBundleFormElement;

/**
 * Factory to inject the ModuleOptions hard dependency
 *
 * @author Fábio Carneiro <fahecs@gmail.com>
 * @license MIT
 */
class TwbBundleFormElementFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->getServiceLocator()->get('Formhandler\Options\ModuleOptions');
        return new TwbBundleFormElement($options);
    }
}
