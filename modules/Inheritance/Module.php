<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 03/07/16
 * Time: 10:42
 */
namespace Inheritance;

use Inheritance\Model\Inheritance;
use Inheritance\Model\InheritanceTable;
use Inheritance\Model\Networking;
use Inheritance\Model\NetworkingTable;
use Inheritance\Model\NetworkingDB;
use Inheritance\Model\NetworkingDBTable;
use Inheritance\Model\Lists;
use Inheritance\Model\ListsTable;
use Inheritance\Controller\ModuleconfigController;
use Zend\Db\ResultSet\ResultSet;
use GenericTools\ZendExtended\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Inheritance\Model\Codes;
use Inheritance\Model\CodesTable;
use Interop\Container\ContainerInterface;

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
     * This is must code for create Model.
     * after you create 2 file in the src/Inheritance/Model folder, you should add this configuration
     * then you can call to model from every controller with
     * @return array
     */
    public function getServiceConfig()
    {

        $serviceConfig = array(
            'factories' => array(
                InheritanceTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Inheritance());
                    $tableGateway = new TableGateway('networking', $dbAdapter, null, $resultSetPrototype);
                    $table = new InheritanceTable($tableGateway);
                    return $table;
                },
                NetworkingTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get('generaldb');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Networking());
                    $tableGateway = new TableGateway('networking', $dbAdapter, null, $resultSetPrototype);
                    $table = new NetworkingTable($tableGateway, $container);
                    return $table;
                },
                NetworkingDBTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get('generaldb');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new networkingDB());
                    $tableGateway = new TableGateway('networking_db', $dbAdapter, null, $resultSetPrototype);
                    $table = new NetworkingTable($tableGateway);
                    return $table;
                },
                ListsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Inheritance());
                    $tableGateway = new TableGateway('list_options', $dbAdapter, null, $resultSetPrototype);
                    $table = new ListsTable($tableGateway);
                    return $table;
                },
                CodesTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Inheritance());
                    $tableGateway = new TableGateway('codes', $dbAdapter, null, $resultSetPrototype);
                    $table = new CodesTable($tableGateway);
                    return $table;
                }
            ),
        );

        return $serviceConfig;
    }


    /**
     * this function run at every creating of controller, and it catch the $controller.
     * here we set global configuration that right to all controllers, we can overwrite this setting in the constarct of every
     * controller (for setting to all the controller ) and overwrite again in the every method if we should.
     * load global variables foe every controllers
     * @param ModuleManager $manager
     */
    public function init(ModuleManager $manager)
    {
        $events = $manager->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            $controller = $e->getTarget();

            //here we put all the global parameter that we should for every view (of course those can overwrite)
            $controller->layout('Inheritance/layout/layout');
            $controller->layout()->setVariable('jsBasePath',  ModuleconfigController::JS_BASE_PATH);
            $controller->layout()->setVariable('cssBasePath',  ModuleconfigController::CSS_BASE_PATH);
	    $openemr_root = array_values(array_filter($GLOBALS['urlArray']));
            $controller->layout()->setVariable('openemr_root', $openemr_root);
            //global variable of language direction
            $controller->layout()->setVariable('language_direction', $_SESSION['language_direction']);
            //Important variable for all layout to config status of form (enable to overwrite in every controller)
            $controller->layout()->setVariable('status', null);
            //variable that get object with all js variables from php
            $controller->layout()->setVariable('jsVariables', json_encode(array()));

        }, 100);
    }



}
