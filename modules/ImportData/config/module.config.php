<?php

use Interop\Container\ContainerInterface;
use ImportData\Controller\ImportDataController;

return array(

    /* declare all controllers */
    'controllers' => array(
        'factories' => [
            ImportDataController::class => function (ContainerInterface $container) {
                return new ImportDataController($container);
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
            'ImportData' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ImportData[/:action][/:type]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'type' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => ImportDataController::class
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ImportData' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'importdata/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        )

    ),
    'view_helpers'=>array(
        'invokables'=>array(
        ),
    ),
    'factories' => array (
    ),

    'service_manager' => array(
        //other services can be registrated here...
        'abstract_factories' => array(
        ),
        //other services can be registrated here...
    ),
    /* register all validators*/
    'validators' => array(
        'invokables' => array(

        ),
    ),

);
