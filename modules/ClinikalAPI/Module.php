<?php
/* +-----------------------------------------------------------------------------+
 * Copyright 2016 matrix israel
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
 *    @author  Oshri Rozmarin <oshri.rozmarin@gmail.com>
 * +------------------------------------------------------------------------------+
 *
 */
namespace ClinikalAPI;

use ClinikalAPI\Service\ApiBuilder;

use Interop\Container\ContainerInterface;
use OpenEMR\Events\RestApiExtend\RestApiCreateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;

use Zend\Db\TableGateway\TableGateway as ZendTableGateway;
use ClinikalAPI\Model\ClinikalPatientTrackingChanges;
use ClinikalAPI\Model\ClinikalPatientTrackingChangesTable;
use ClinikalAPI\Model\FormContextMap;
use ClinikalAPI\Model\FormContextMapTable;
use ClinikalAPI\Model\GetTemplatesService;
use ClinikalAPI\Model\GetTemplatesServiceTable;

class Module {


    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,

                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }


    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                ApiBuilder::class =>  function(ContainerInterface $container) {
                    $model = new ApiBuilder($container);
                    return $model;
                },
                ClinikalPatientTrackingChangesTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ClinikalPatientTrackingChanges());
                    $tableGateway = new ZendTableGateway('clinikal_patient_tracking_changes', $dbAdapter, null, $resultSetPrototype);
                    $table = new ClinikalPatientTrackingChangesTable($tableGateway);
                    return $table;
                },
                FormContextMapTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FormContextMap());
                    $tableGateway = new ZendTableGateway('form_context_map', $dbAdapter, null, $resultSetPrototype);
                    $table = new FormContextMapTable($tableGateway);
                    return $table;
                },
                GetTemplatesServiceTable::class =>  function(ContainerInterface $container) {
                    xdebug_break();
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new GetTemplatesService());
                    $tableGateway = new ZendTableGateway('clinikal_templates_map', $dbAdapter, null, $resultSetPrototype);
                    $table = new GetTemplatesServiceTable($tableGateway);
                    return $table;
                },
            ),
        );
    }


    /**
     * @param MvcEvent $e
     *
     * Register our event listeners here
     */
    public function onBootstrap(MvcEvent $e)
    {
        // Get application service manager and get instance of event dispatcher
        $serviceManager = $e->getApplication()->getServiceManager();
        $oemrDispatcher = $serviceManager->get(EventDispatcherInterface::class);
        $this->sm = $serviceManager;
        // listen for view events for routes in zend_modules
        $oemrDispatcher->addListener(RestApiCreateEvent::EVENT_HANDLE, [$this, 'initApi']);
    }


    /**
     * @param Adapter $m
     *
     * Generate and register in route api calls
     */
    public function initApi($m)
    {
        $apiBuilder= $this->sm->get(ApiBuilder::class);
        $extend_route_map=$apiBuilder->getApi();

        if (count($extend_route_map) > 0) {
            foreach ($extend_route_map as $route => $action) {
                $m->addToRouteMap($route, $action);
            }
        }


    }



}
