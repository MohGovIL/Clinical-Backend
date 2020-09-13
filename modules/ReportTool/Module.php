<?php

namespace ReportTool;


use ReportTool\View\Helper\DrawTable;
use function PHPSTORM_META\type;
use Zend\ModuleManager\ModuleManager;
use Zend\Db\ResultSet\ResultSet;
use GenericTools\ZendExtended\TableGateway;
use Interop\Container\ContainerInterface;

use GenericTools\Model\Registry;
use GenericTools\Model\RegistryTable;

/**
 * The default module configurator
 *
 * @author suleymanmelikoglu
 */
class Module
{

    /**
     * the implementation of the autoloader provider,
     * returns an array for the AutoloaderFactory
     */
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
                RegistryTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Registry());
                    $tableGateway = new TableGateway('registry', $dbAdapter, null, $resultSetPrototype);
                    $table = new RegistryTable($tableGateway);
                    return $table;
                },
            ),
        );
    }

    /**
     * load global variables foe every controllers
     * @param ModuleManager $manager
     */
    public function init(ModuleManager $manager)
    {
        $events = $manager->getEventManager();
        $sharedEvents = $events->getSharedManager();

        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function ($e) {
            $controller = $e->getTarget();
            //$controller->layout()->setVariable('status', null);
            $controller->layout('ReportTool/layout/layout');

            //global variable of language direction
            $controller->layout()->setVariable('language_direction', $_SESSION['language_direction']);

            //global variable of title
            $controller->layout()->setVariable('title', 'Report tool');
        }, 100);
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'reports_draw_table' => function(ContainerInterface $container) {
                    // Get the shared service manager instance
                    $dbAdapter = $container->get('Zend\Db\Adapter\Adapter');
                    // Now inject it into the view helper constructor
                    $table= new DrawTable();
                    $table->setDbAdapter( $dbAdapter);
                    return $table;

                },
            ),

        );
    }

}
