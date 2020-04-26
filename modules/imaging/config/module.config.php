<?php
/* +-----------------------------------------------------------------------------+
 * Copyright 2019 matrix israel
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see
 * http://www.gnu.org/licenses/licenses.html#GPL
 *    @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 * +------------------------------------------------------------------------------+
 *
 */

use ClinikalAPI\Controller\ManagerApi;
use Interop\Container\ContainerInterface;

return array(

    /* declare all controllers */

    'controllers' => array(
        'factories' => [
            ManagerApi::class => function (ContainerInterface $container) {
                return new ManagerApi($container);
            },
        ],

    ),

    /**
     * routing configuration.
     * for more option and details - http://zf2.readthedocs.io/en/latest/in-depth-guide/understanding-routing.html?highlight=routing
     */
    'router' => array(
        'routes' => array(
            'manage_api' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/imaging-api[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => ManagerApi::class,
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),


    'view_manager' => array(

    )
);
