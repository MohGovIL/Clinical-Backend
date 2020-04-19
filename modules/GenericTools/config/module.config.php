<?php

use Interop\Container\ContainerInterface;
use GenericTools\Controller\BaseController;
use GenericTools\Controller\GenericToolsController;

return array(
    'controllers' => array(
        'factories' => [
            BaseController::class => function (ContainerInterface $container) {
                return new BaseController($container);
            },
            GenericToolsController::class => function (ContainerInterface $container) {
                return new GenericToolsController($container);
            },
        ]
    ),
    'router' => array(
        'routes' => array(
            'generic-tools' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/generic-tools[/:action][/:param]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => GenericToolsController::class,
                        'action'     => 'index'
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'GenericTools' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'GenericTools/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        )

    ),
);

