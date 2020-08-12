<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 03/07/16
 * Time: 10:42
 */
namespace Formhandler;

use Formhandler\Controller\ModuleconfigController;
use Formhandler\View\Helper\CreateCustomControlFromList;
use Formhandler\View\Helper\CurrentFamilyTable;
use Formhandler\View\Helper\DrugAndAlcoholUsageTable;

use Formhandler\View\Helper\GenerateCustomInputWithData;
use Formhandler\View\Helper\GetLastPsychosocialTreatment;
use Formhandler\View\Helper\MaxDosageHelper;
use Formhandler\View\Helper\GenericTable;
use Formhandler\View\Helper\SideEffectGenericTable;
use Formhandler\View\Helper\Form\TwbBundleFormUrl;

use Formhandler\View\Helper\HelperFactory;


use Laminas\Mvc\MvcEvent;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Events\RestApiExtend\RestApiCreateEvent;
use OpenEMR\Services\Globals\GlobalSetting;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function GuzzleHttp\Psr7\parse_request;
use Interop\Container\ContainerInterface;
use Laminas\ModuleManager\ModuleManager;


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
                'Formhandler\Model\ExampleTable' =>  function(ContainerInterface $container) {
                    $tableGateway = $container->get('ExampleTableGateway');
                    $table = new PumpsTable($tableGateway);
                    return $table;
                },
                'ExampleTableGateway' => function (ContainerInterface $container) {
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Examle());
                    return new TableGateway('sql_table_name', $dbAdapter, null, $resultSetPrototype);
                },
            ),
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
            $controller->layout('formhandler/layout/layout');
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
        $this->container = $serviceManager;
        // listen for view events for routes in zend_modules
        $oemrDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, [$this, 'addCustomGlobals']);
    }

    public function addCustomGlobals(GlobalsInitializedEvent $event)
    {
        /*******************************************************************/
        $event->getGlobalsService()->createSection("Formhandler settings", "Connectors");
        $setting = new GlobalSetting("Formhandler -load forms settings from CouchDB", 'bool', 0, "Formhandler -load forms settings from CouchDB");
        $event->getGlobalsService()->appendToSection("Formhandler settings", "formhandler_couchdb", $setting);
        /*******************************************************************/
    }

        public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'drug_and_alchol_table' => function(ContainerInterface $container) {
                    // Get the shared service manager instance
                     //$sm =  $container->getServiceLocator();
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $table= new DrugAndAlcoholUsageTable();
                    $table->setDbAdapter( $dbAdapter);
                    return $table;

                },

                'generate_custom_input_with_data' => function(ContainerInterface $container) {
                    // Get the shared service manager instance
                     //$sm =  $container->getServiceLocator();
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $CustomInput= new GenerateCustomInputWithData();
                    $CustomInput->setDbAdapter( $dbAdapter);
                    return $CustomInput;

                },
                'create_custom_control_from_list'=> function(ContainerInterface $container) {
                    // Get the shared service manager instance
                     //$sm =  $container->getServiceLocator();
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $CustomInput= new CreateCustomControlFromList();
                    $CustomInput->setDbAdapter( $dbAdapter);
                    return $CustomInput;

                },
                'max_dosage_helper' => function($sl = null) {
                    // Get the shared service manager instance
                    // Now inject it into the view helper constructor
                    $CustomInput= new MaxDosageHelper();
                    return $CustomInput;

                },
                'current_family_table'=> function(ContainerInterface $container) {
                    // Get the shared service manager instance
                     //$sm =  $container->getServiceLocator();
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $CustomInput= new CurrentFamilyTable();
                    $CustomInput->setDbAdapter( $dbAdapter);
                    return $CustomInput;

                },
                'get_last_psychosocial_treatment'=>function(ContainerInterface $container){

                     //$sm =  $container->getServiceLocator();
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $CustomInput= new GetLastPsychosocialTreatment();
                    $CustomInput->setDbAdapter( $dbAdapter);
                    return $CustomInput;

                },
                'generic_table' => function(ContainerInterface $container) {
                        // Get the shared service manager instance
                         //$sm =  $container->getServiceLocator();
                        $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                        // Now inject it into the view helper constructor
                        $table= new GenericTable($container);
                        $table->setDbAdapter( $dbAdapter);

                        $get_array=[];
                        parse_str($_SERVER['QUERY_STRING'], $get_array);
                        $table->setEditParams($get_array);

                    return $table;

                    },
                'helper_factory' => function(ContainerInterface $container) {
                    // Get the shared service manager instance
                     //$sm =  $container->getServiceLocator();
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $table= new HelperFactory($container);
                    $table->setDbAdapter( $dbAdapter);

                    $get_array=[];
                    parse_str($_SERVER['QUERY_STRING'], $get_array);
                    $table->setEditParams($get_array);

                    return $table;

                },
                'side_effect_generic_table' => function(ContainerInterface $container) {
                    // Get the shared service manager instance
                     //$sm =  $container->getServiceLocator();
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $table= new SideEffectGenericTable($container);
                    $table->setDbAdapter( $dbAdapter);

                    $get_array=[];
                    parse_str($_SERVER['QUERY_STRING'], $get_array);
                    $table->setEditParams($get_array);

                    return $table;

                },




            ),
        );
    }

}
