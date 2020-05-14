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
namespace FhirAPI;

use FhirAPI\FhirRestApiBuilder\Builders\OrganizationBuilder;
use FhirAPI\FhirRestApiBuilder\Director;


use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\Service\FhirApiBuilder;

use FhirAPI\Service\FhirValidateTypes;
use GenericTools\Model\FhirRestElements;
use GenericTools\Model\LogServiceTable;
use GenericTools\Model\PatientsTable;
use GenericTools\Service\AclCheckExtendedService;
use Interop\Container\ContainerInterface;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Events\RestApiExtend\RestApiCreateEvent;
use OpenEMR\Services\Globals\GlobalSetting;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Laminas\Mvc\MvcEvent;
use FhirAPI\Service\FhirRequestParamsHandler;



class Module {


    private static $version = 'v4';

    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Laminas\Loader\StandardAutoloader' => array(
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
                FhirApiBuilder::class =>  function(ContainerInterface $container) {
                    $model = new FhirApiBuilder($container);
                    return $model;
                },
                FhirRequestParamsHandler::class =>  function() {
                    $model = new FhirRequestParamsHandler();
                    return $model;
                },
                FhirValidateTypes::class =>  function() {
                    $model = new FhirValidateTypes();
                    return $model;
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
        $this->container = $this->sm ;//$oemrDispatcher->getContainer();
       //
        $this->container = $serviceManager;
        // listen for view events for routes in zend_modules
        $oemrDispatcher->addListener(RestApiCreateEvent::EVENT_HANDLE, [$this, 'initFhirApi']);
        $oemrDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, [$this, 'addCustomGlobals']);
    }


    public function addCustomGlobals(GlobalsInitializedEvent $event) {

        $event->getGlobalsService()->createSection("Fetch Files", "Connectors");

        $setting = new GlobalSetting( "Enable FHIR Type Validation", 'bool', 1, "When checked, run fhir native validation are active" );
        $event->getGlobalsService()->appendToSection( "Connectors", "fhir_type_validation", $setting );
    }


    public function getAllFhirRestBuilders(){
        $pSQL = sqlStatement("SELECT * FROM fhir_rest_elements WHERE active = 1 ");
        // $pRow = sqlFetchArray($pSQL);
        // return (empty($pRow) ? '' : $pRow['ptitle'] . ' ' . $pRow['pfname'] . ' ' . $pRow['pmname'] . ' ' . $pRow['plname']);
        while ($lrow = sqlFetchArray($pSQL)) {
            $builder = "FhirAPI\FhirRestApiBuilder\Builders\\".$lrow['name']."Builder";
            $resourceBuilder = new $builder(self::$version);
            $builder::setContainer($this->container);
            (new Director())->build($resourceBuilder);
        }
    }
    /**
     * @param Adapter $m
     *
     * Generate and register in route api calls
     */
    public function initFhirApi($m)
    {
       $this->checkAcl();

        $this->getAllFhirRestBuilders();
        $extend_route_map=Restful::getAllRoutes();
        if (count($extend_route_map) > 0) {
            foreach ($extend_route_map as $route => $action) {
                $m->addToFHIRRouteMap($route, $action);
            }
        }
    }



    public function checkAcl()
    {

        $requestType=strtolower($_SERVER['REQUEST_METHOD']);
        $path=parse_url($_SERVER['REQUEST_URI'])['path'];

        $urlbreakdown =explode('/',$path);

        $fhirElement="";
        //$apiPos="-1";
        $fhirEntityPos="-1";

        foreach ($urlbreakdown as $pos =>$val){

            /*
            if($val==="apis"){
                $apiPos=$pos;
            }
            */

            if($val==="fhir"){
                $fhirEntityPos=$pos+1; // usually the version
            }

            if($val===self::$version){
                $fhirElement=strtolower($urlbreakdown[$pos+1]);
                break;
            }
        }

        $permittedFhir=array(self::$version,'auth'); // this is a whiteList of fhir requests
        if ($fhirEntityPos!=="-1" && !in_array($urlbreakdown[$fhirEntityPos],$permittedFhir)){
            ErrorCodes::http_response_code('401','Unauthorized fhir request');
        }

        if($fhirElement!==""){

            $AclCheckExtendedService= $this->container->get(AclCheckExtendedService::class);

            if($requestType==="get"){
                if( !$AclCheckExtendedService->authorizationCheck('fhir_api',$fhirElement,false,'write')
                    && !$AclCheckExtendedService->authorizationCheck('fhir_api',$fhirElement,false,'view')
                  ){
                    ErrorCodes::http_response_code('401','Unauthorized');
                }
            }else{
                if(!$AclCheckExtendedService->authorizationCheck('fhir_api',$fhirElement,false,'write')){
                    ErrorCodes::http_response_code('401','Unauthorized');
                }
            }

        }

    }

}
