<?php

use Interop\Container\ContainerInterface;
use Inheritance\Controller\InheritanceController;

return array(

    /* declare all controllers */
    'controllers' => array(
        'factories' => [
            InheritanceController::class => function (ContainerInterface $container) {
                return new InheritanceController($container);
            }
        ]
    ),

    /**
     * routing configuration.
     * you can adding parameters to url [/:action][/:id] (they called in the controller with $this->params()->fromRoute('id');)
     * for more option and details - http://zf2.readthedocs.io/en/latest/in-depth-guide/understanding-routing.html?highlight=routing
     */
    'router' => array(
        'routes' => array(
            'inheritance' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/inheritance[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => InheritanceController::class,
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),


    'view_manager' => array(
        'template_path_stack' => array(
            'Inheritance' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'Inheritance/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        )
    ),
);