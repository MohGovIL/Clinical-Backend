<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 03/07/16
 * Time: 10:42
 */
namespace ImportData;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use ImportData\Controller\ModuleconfigController;
use ImportData\Controller\ImportDataController;
use Laminas\ModuleManager\ModuleManager;
use ImportData\Model\ImportDataTable;
use ImportData\Model\ImportData;
use ImportData\Model\Lists;
use ImportData\Model\ListsTable;
use ImportData\Model\LangConstants;
use ImportData\Model\LangConstantsTable;
use ImportData\Model\LangDefinitions;
use ImportData\Model\LangDefinitionsTable;
use ImportData\Model\ImportDataLog;
use ImportData\Model\ImportDataLogTable;
use ImportData\Model\Codes;
use ImportData\Model\CodesTable;
use Interop\Container\ContainerInterface;


class Module {

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
     * This is must code for create Model.
     * after you create 2 file in the src/Formhandler/Model folder, you should add this configuration
     * then you can call to model from every controller with
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                ImportDataTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ImportData());
                    $tableGateway = new TableGateway('moh_import_data', $dbAdapter, null, $resultSetPrototype);
                    $table = new ImportDataTable($tableGateway);
                    return $table;
                },
                ListsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Lists());
                    $tableGateway = new TableGateway('list_options', $dbAdapter, null, $resultSetPrototype);
                    $table = new ListsTable($tableGateway);
                    return $table;
                },
                CodesTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Codes());
                    $tableGateway = new TableGateway('codes', $dbAdapter, null, $resultSetPrototype);
                    $table = new CodesTable($tableGateway);
                    return $table;
                },
                LangConstantsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LangConstants());
                    $tableGateway = new TableGateway('lang_constants', $dbAdapter, null, $resultSetPrototype);
                    $table = new LangConstantsTable($tableGateway);
                    return $table;
                },
                LangDefinitionsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LangDefinitions());
                    $tableGateway = new TableGateway('lang_definitions', $dbAdapter, null, $resultSetPrototype);
                    $table = new LangDefinitionsTable($tableGateway);
                    return $table;
                },
                ImportDataLogTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ImportDataLog());
                    $tableGateway = new TableGateway('moh_import_data_log', $dbAdapter, null, $resultSetPrototype);
                    $table = new ImportDataLogTable($tableGateway);
                    return $table;
                },
            )
        );
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
            $controller->layout('importdata/layout/layout');
            $controller->layout()->setVariable('jsBasePath',  ModuleconfigController::JS_BASE_PATH);
            $controller->layout()->setVariable('cssBasePath',  ModuleconfigController::CSS_BASE_PATH);
            //global variable of language direction
            $controller->layout()->setVariable('language_direction', $_SESSION['language_direction']);
            //Important variable for all layout to config status of form (enable to overwrite in every controller)
            $controller->layout()->setVariable('status', null);
            //variable that get object with all js variables from php
            $controller->layout()->setVariable('jsVariables', json_encode(array()));

        }, 100);
    }

    public function getViewHelperConfig()
    {
        return array(

        );
    }

}
