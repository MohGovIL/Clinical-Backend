<?php

namespace GenericTools;

use Errors\Plugin\Errors;
use GenericTools\Model\AclTables;
use GenericTools\Model\FhirRestElements;
use GenericTools\Model\FhirRestElementsTableTable;
use GenericTools\Model\FormEncounter;
use GenericTools\Model\FormEncounterTable;
use GenericTools\Model\Lists;
use GenericTools\Model\ListsTable;
use GenericTools\Model\Patients;
use GenericTools\Model\PatientsTable;
use GenericTools\Model\User;
use GenericTools\Model\UserTable;
use GenericTools\Model\Facility;
use GenericTools\Model\FacilityTable;
use GenericTools\Model\LangLanguages;
use GenericTools\Model\LangLanguagesTable;
use GenericTools\Service\CouchdbService;
use GenericTools\Service\MailService;
use GenericTools\Service\PdfService;
use GenericTools\Service\ExcelService;
use GenericTools\Service\FormLogService;
use GenericTools\Helpers\ArrivalDaysParserHelper;
use GenericTools\Helpers\ClinicInfoParserHelper;
use GenericTools\Model\PostcalendarCategories;
use GenericTools\Model\PostcalendarCategoriesTable;

use GenericTools\Model\ListsOpenEmr;
use GenericTools\Model\ListsOpenEmrTable;
use GenericTools\Controller\GenericToolsExtended;
use Laminas\Db\ResultSet\ResultSet;
use GenericTools\ZendExtended\TableGateway;
use Laminas\ModuleManager\ModuleManager;
use Application\Listener\Listener;
use Laminas\Mvc\MvcEvent;
use Interop\Container\ContainerInterface;
use GenericTools\Model\PostcalendarEvents;
use GenericTools\Model\PostcalendarEventsTable;
use GenericTools\Model\HealthcareServices;
use GenericTools\Model\HealthcareServicesTable;
use GenericTools\Model\ValueSets;
use GenericTools\Model\ValueSetsTable;

use GenericTools\Model\EventCodeReasonMapTable;
use GenericTools\Model\EventCodeReasonMap;
use GenericTools\Model\Documents;
use GenericTools\Model\DocumentsTable;
use GenericTools\Model\DocumentsCategoriesTable;

use GenericTools\Model\EncounterReasonCodeMapTable;
use GenericTools\Model\EncounterReasonCodeMap;
use GenericTools\Service\AclCheckExtendedService;

use GenericTools\Model\RegistryTable;
use GenericTools\Model\Registry;

use GenericTools\Model\LogServiceTable;
use GenericTools\Model\LogService;


// todo: move the following to FHIR Module.php
use GenericTools\Model\RelatedPerson;
use GenericTools\Model\RelatedPersonTable;
use GenericTools\Model\FormsGenericHandlerTable;
//********************************************
use GenericTools\Model\FormVitals;
use GenericTools\Model\FormVitalsTable;






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
                PatientsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Patients());
                    $tableGateway = new TableGateway('patient_data', $dbAdapter, null, $resultSetPrototype);
                    $table = new PatientsTable($tableGateway);
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
                UserTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    $tableGateway = new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                FacilityTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Facility());
                    $tableGateway = new TableGateway('facility', $dbAdapter, null, $resultSetPrototype);
                    $table = new FacilityTable($tableGateway);
                    return $table;
                },
                LangLanguagesTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LangLanguages());
                    $tableGateway = new TableGateway('lang_languages', $dbAdapter, null, $resultSetPrototype);
                    $table = new LangLanguagesTable($tableGateway);
                    return $table;
                },
                ListsOpenEmrTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ListsOpenEmr());
                    $tableGateway = new TableGateway('lists', $dbAdapter, null, $resultSetPrototype);
                    $table = new ListsOpenEmrTable($tableGateway);
                    return $table;
                },
                PostcalendarEventsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PostcalendarEvents());
                    $tableGateway = new TableGateway('openemr_postcalendar_events', $dbAdapter, null, $resultSetPrototype);
                    $table = new PostcalendarEventsTable($tableGateway);
                    return $table;
                },
                MailService::class =>  function(ContainerInterface $container) {
                    $service = new MailService($container);
                    return $service;
                },
                PdfService::class =>  function(ContainerInterface $container) {
                    $service = new PdfService($container);
                    return $service;
                },
                ExcelService::class =>  function(ContainerInterface $container) {
                    $service = new ExcelService($container);
                    return $service;
                },
                FormLogService::class =>  function(ContainerInterface $container) {
                    $service = new FormLogService($container);
                    return $service;
                },
                CouchdbService::class =>  function(ContainerInterface $container) {
                    $service = new CouchdbService($container);
                    return $service;
                },
                ArrivalDaysParserHelper::class =>  function() {
                    $helper = new ArrivalDaysParserHelper();
                    return $helper;
                },
                ClinicInfoParserHelper::class =>  function() {
                    $helper = new ClinicInfoParserHelper();
                    return $helper;
                },
                PostcalendarCategoriesTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PostcalendarCategories());
                    $tableGateway = new TableGateway('openemr_postcalendar_categories', $dbAdapter, null, $resultSetPrototype);
                    $table = new PostcalendarCategoriesTable($tableGateway);
                    return $table;
                },
                HealthcareServicesTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new HealthcareServices());
                    $tableGateway = new TableGateway('fhir_healthcare_services', $dbAdapter, null, $resultSetPrototype);
                    $table = new HealthcareServicesTable($tableGateway);
                    return $table;
                },
                AclTables::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');
                    return new AclTables($dbAdapter);
                },
                FormEncounterTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FormEncounter());
                    $tableGateway = new TableGateway('form_encounter', $dbAdapter, null, $resultSetPrototype);
                    $table = new FormEncounterTable($tableGateway);
                    return $table;
                },
                FhirRestElements::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FhirRestElements());
                    $tableGateway = new TableGateway('fhir_rest_elements', $dbAdapter, null, $resultSetPrototype);
                    $table = new FhirRestElementsTableTable($tableGateway);
                    return $table;
                },
                ValueSetsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ValueSets());
                    $tableGateway = new TableGateway('fhir_value_sets', $dbAdapter, null, $resultSetPrototype);
                    $table = new ValueSetsTable($tableGateway);
                    return $table;
                },
                EventCodeReasonMapTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EventCodeReasonMap());
                    $tableGateway = new TableGateway('event_codeReason_map', $dbAdapter, null, $resultSetPrototype);
                    $table = new EventCodeReasonMapTable($tableGateway);
                    return $table;
                },
                DocumentsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Documents());
                    $tableGateway = new TableGateway('documents', $dbAdapter, null, $resultSetPrototype);
                    $table = new DocumentsTable($tableGateway);
                    return $table;
                },
                DocumentsCategoriesTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Documents());
                    $tableGateway = new TableGateway('categories_to_documents', $dbAdapter, null, $resultSetPrototype);
                    $table = new DocumentsCategoriesTable($tableGateway);
                    return $table;
                },
                RelatedPersonTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RelatedPerson());
                    $tableGateway = new TableGateway('related_person', $dbAdapter, null, $resultSetPrototype);
                    $table = new RelatedPersonTable($tableGateway);
                    return $table;
                },
                EncounterReasonCodeMapTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EncounterReasonCodeMap());
                    $tableGateway = new TableGateway('encounter_reasoncode_map', $dbAdapter, null, $resultSetPrototype);
                    $table = new EncounterReasonCodeMapTable($tableGateway);
                    return $table;
                },
                RegistryTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Registry());
                    $tableGateway = new TableGateway('registry', $dbAdapter, null, $resultSetPrototype);
                    $table = new RegistryTable($tableGateway);
                    return $table;
                },
                FormsGenericHandlerTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $table = new FormsGenericHandlerTable($dbAdapter);
                    return $table;
                },
                AclCheckExtendedService::class =>  function(ContainerInterface $container) {
                    $service = new AclCheckExtendedService($container);
                    return $service;
                },
                LogServiceTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LogService());
                    $tableGateway = new TableGateway('log', $dbAdapter, null, $resultSetPrototype);
                    $table = new LogServiceTable($tableGateway);
                    return $table;
                },
                FormVitalsTable::class =>  function(ContainerInterface $container) {
                    $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FormVitals());
                    $tableGateway = new TableGateway('form_vitals', $dbAdapter, null, $resultSetPrototype);
                    $table = new FormVitalsTable($tableGateway);
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
            $controller->layout('GenericTools/layout/layout');

            //global variable of language direction
            $controller->layout()->setVariable('language_direction', $_SESSION['language_direction']);

            //global variable of title
            $controller->layout()->setVariable('title', 'Generic Tools');

            // autoloader for ui-components from clinikal folder.
            spl_autoload_register(function ($class) {
                require_once $GLOBALS['fileroot'].'/clinikal/ui-components/' . str_replace("\\", '/', $class) . '.php';
            });

        }, 100);
    }

}

