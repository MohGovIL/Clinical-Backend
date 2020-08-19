<?php

use Interop\Container\ContainerInterface;

return array(

    'controllers' => array(
        'factories' => [

        ]
    ),
    'router' => array(
        'routes' => array(
        ),
    ),
    'invokables'=>array(

    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ReportTool' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'ReportTool/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        )

    ),

);
