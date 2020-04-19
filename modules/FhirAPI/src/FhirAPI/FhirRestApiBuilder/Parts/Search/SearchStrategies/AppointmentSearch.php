<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\HealthcareServicesTable;
use GenericTools\Model\PostcalendarEventsTable;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Patient\Patient;

class AppointmentSearch extends BaseSearch
{

    CONST PATIENT_STRING = 'patient';
    CONST STATUS_STRING = 'status';
    CONST DATE_STRING = 'date';
    CONST HS_STRING = 'actor:HealthcareService';
    CONST HS_ORG_STRING = 'actor:HealthcareService.organization';
    CONST SORT_STRING = '_sort';
    CONST COUNT_STRING= '_count';
    CONST SUMMARY_STRING = '_summary';
    CONST INCLUDE_STRING = '_include';
    CONST ID_STRING = '_id';
    protected $mapping;
    protected $container;
    protected $params;
    private $filters;
    private $fromDate;
    private $toDate;
    private $allSorts;
    private $statuses;
    private $healthcareServicesTable;

    /**
     * AppointmentSearch constructor.
     */
    public function __construct($parameters)
    {
        parent::__construct($parameters);
        $this->mapping = $parameters['fhirObj'];
        $this->container = $parameters['container'];

        $this->FHIRBundle = $this->mapping->createSearchBundle();
        $this->params = $this->paramsFromBody;
        $this->filters = array();
        $this->fromDate = null;
        $this->toDate = null;
        $this->allSorts = array();
        $this->statuses = $this->mapping->getApptStatuses();
        $this->healthcareServicesTable = $this->container->get(HealthcareServicesTable::class);
        $this->fromDate = '0000-00-00';
        $this->toDate = '9999-12-31';
    }

    /**
     * Go over request arguments and preform db search
     *
     * @return FHIRBundle
     */
    public function search()
    {
        /* before search from db*/
        if (is_array($this->params) && key_exists('ARGUMENTS', $this->params)) {
            // it is important to do special handlers before default handling
            // special handlers
            $rez = $this->statusHandler($this->params['ARGUMENTS']);
            $rez = $this->patientHandler($this->params['ARGUMENTS']);
            $rez = $this->dateHandler($this->params['ARGUMENTS']);
            $rez = $this->healthCareServiceHandler($this->params['ARGUMENTS']);
            $rez = $this->organizationHandler($this->params['ARGUMENTS']);
            // if no match between healthCareService & organizationHandler
            if (!$rez) {return $this->notFoundError();}
            //default handling
            $rez = $this->basicFieldsHandler($this->params['ARGUMENTS']);
        }

        if (is_array($this->params) && key_exists('PARAMETERS_FOR_SEARCH_RESULT', $this->params)) {
            $parametersForSearch = $this->params['PARAMETERS_FOR_SEARCH_RESULT'];
            $rez = $this->sortHandler($parametersForSearch);
        }

        if (is_array($this->params) && key_exists('PARAMETERS_FOR_ALL_RESOURCES', $this->params)) {
            $parametersForSearch = $this->params['PARAMETERS_FOR_ALL_RESOURCES'];
            $rez = $this->idHandler($parametersForSearch);
        }

        if (is_array($this->params) && key_exists('PARAMETERS_FOR_SEARCH_RESULT', $this->params)) {
            $parametersForSearch = $this->params['PARAMETERS_FOR_SEARCH_RESULT'];
            $rez = $this->countHandler($parametersForSearch);
        }



        /* do db search */
        $appointments = $this->getAppointmentsByParams($this->fromDate, $this->toDate, $this->filters, $this->allSorts);

        /* add db results to bundle */
        foreach ($appointments as $index => $apt) {
            $FHIRResourceContainer = new FHIRResourceContainer($apt);
            $this->FHIRBundle = $this->mapping->addResourceToBundle($this->FHIRBundle, $FHIRResourceContainer, 'match');
        }

        /* after search from db*/
        if (is_array($this->params) && key_exists('PARAMETERS_FOR_SEARCH_RESULT', $this->params)) {
            $parametersForSearch = $this->params['PARAMETERS_FOR_SEARCH_RESULT'];
            $rez = $this->includeHandler($parametersForSearch);
            $summary = $this->summaryHandler($parametersForSearch);
        }

        $this->params['REWRITE_COMMAND']=array();
        // check if all params was handled
        foreach ($this->params as $lineName => $data){
            if (is_array($data)&&!empty($data)){
                return $this->badRequestError();
            }
        }
        // return search results
        if ($summary !== false) {
            return $summary;
        } else {
            return $this->FHIRBundle;
        }
    }


    private function basicFieldsHandler($searchArg)
    {


        foreach ($searchArg as $field => $data) {
            //$field=array_keys($searchArg)[0];
            $dbfield= $this->mapping->convertFieldsToDB(array($field=>'RETURN_DB_NAME'));

            if (is_array($dbfield) && !empty($dbfield)) {
                $dateArr = $searchArg[$field];
                foreach ($dateArr as $key => $searchterm) {

                    if ($searchterm['modifier'] == 'exact' && $searchterm['operator'] == null) {
                        unset($this->params['ARGUMENTS'][$field][$key]);
                        $this->filters[$dbfield[$field]] = $searchterm['value'];
                        //continue;
                    }
                }

                if (empty($this->params['ARGUMENTS'][$field])) {
                    unset($this->params['ARGUMENTS'][$field]);
                }
            }
        }



        return true;
    }



    private function patientHandler($searchArg)
    {
        if (is_array($searchArg) && key_exists(self::PATIENT_STRING, $searchArg)) {

            $dateArr = $searchArg[self::PATIENT_STRING];
            foreach ($dateArr as $key => $status) {

                if ($status['modifier'] == 'exact' && $status['operator'] == null) {
                    unset($this->params['ARGUMENTS'][self::PATIENT_STRING][$key]);
                    $this->filters['pc_pid'] = $status['value'];
                    continue;
                }
            }
                if (empty($this->params['ARGUMENTS'][self::PATIENT_STRING])) {
                    unset($this->params['ARGUMENTS'][self::PATIENT_STRING]);
                }

        }
        return true;
    }



    private function statusHandler($searchArg)
    {
        if (is_array($searchArg) && key_exists(self::STATUS_STRING, $searchArg)) {

            $dateArr = $searchArg[self::STATUS_STRING];
            foreach ($dateArr as $key => $status) {

                if ($status['modifier'] == 'exact' && $status['operator'] == null) {
                    unset($this->params['ARGUMENTS'][self::STATUS_STRING][$key]);
                    $this->filters['pc_apptstatus'] = $status['value'];
                    continue;
                }

                if ($status['modifier'] === 'not') {
                    unset($this->params['ARGUMENTS'][self::STATUS_STRING][$key]);
                    unset($this->statuses[$status['value']]);
                    $filteredStatuses = array_keys($this->statuses);
                    $this->filters['pc_apptstatus'] = $filteredStatuses;
                }
            }

            if (empty($this->params['ARGUMENTS'][self::STATUS_STRING])) {
                unset($this->params['ARGUMENTS'][self::STATUS_STRING]);
            }
        }
        return true;
    }

    private function dateHandler($searchArg)
    {

        if (is_array($searchArg) && key_exists(self::DATE_STRING, $searchArg)) {
            $dateArr = $searchArg[self::DATE_STRING];
            foreach ($dateArr as $key => $date) {
                if ($date['operator'] === 'ge') {
                    unset($this->params['ARGUMENTS'][self::DATE_STRING][$key]);
                    $this->fromDate = $date['value'];
                    continue;
                }
                if ($date['operator'] === 'le') {
                    unset($this->params['ARGUMENTS'][self::DATE_STRING][$key]);
                    $this->toDate = $date['value'];
                    continue;
                }
                if ($date['operator'] === 'eq' || $date['operator'] == null) {
                    unset($this->params['ARGUMENTS'][self::DATE_STRING][$key]);
                    $this->toDate = $date['value'];
                    $this->fromDate = $date['value'];
                }
            }

            if (empty($this->params['ARGUMENTS'][self::DATE_STRING])) {
                unset($this->params['ARGUMENTS'][self::DATE_STRING]);
            }
        }
        return true;
    }

    private function healthCareServiceHandler($searchArg)
    {

        if (is_array($searchArg) && key_exists(self::HS_STRING, $searchArg)) {

            $healthcareServicesArr = $searchArg[self::HS_STRING];
            $tempArray = array();
            foreach ($healthcareServicesArr as $key => $val) {
                $tempArray[] = $val['value'];
                unset($this->params['ARGUMENTS'][self::HS_STRING][$key]);
            }

            if (empty($tempArray)) {
                return false;
            }
            if (empty($this->filters['pc_healthcare_service_id'])) {
                $this->filters['pc_healthcare_service_id'] = $tempArray;
            } else {
                $this->filters['pc_healthcare_service_id'] = array_intersect($this->filters['pc_healthcare_service_id'], $tempArray);
                if (empty($this->filters['pc_healthcare_service_id'])) {
                    return false;//return no matches
                }
            }

            if (empty($this->params['ARGUMENTS']['actor:HealthcareService'])) {
                unset($this->params['ARGUMENTS']['actor:HealthcareService']);
            }
        }
        return true;
    }

    private function organizationHandler($searchArg)
    {

        if (is_array($searchArg) && key_exists(self::HS_ORG_STRING, $searchArg)) {

            $healthcareServicesArr = $searchArg[self::HS_ORG_STRING];
            $tempArray = array();
            foreach ($healthcareServicesArr as $key => $val) {
                $organizationId = $val['value'];
                $results = $this->healthcareServicesTable->buildGenericSelect(['providedBy' => $organizationId]);
                foreach ($results as $org){
                    $ids[] = $org['id'];
                }
                $tempArray = array_merge($tempArray, $ids);
                unset($this->params['ARGUMENTS'][self::HS_ORG_STRING][$key]);

            }

            if (empty($tempArray)) {
                return false;
            }
            if (empty($this->filters['pc_healthcare_service_id'])) {
                $this->filters['pc_healthcare_service_id'] = $tempArray;
            } else {
                $this->filters['pc_healthcare_service_id'] = array_intersect($this->filters['pc_healthcare_service_id'], $tempArray);
                if (empty($this->filters['pc_healthcare_service_id'])) {
                    return false;//return no matches
                }
            }

            if (empty($this->params['ARGUMENTS'][self::HS_ORG_STRING])) {
                unset($this->params['ARGUMENTS'][self::HS_ORG_STRING]);
            }
        }
        return true;
    }

    private function summaryHandler($searchArg)
    {
        $return = false;
        if (is_array($searchArg) && key_exists(self::SUMMARY_STRING, $searchArg)) {
            $tempParam = $searchArg[self::SUMMARY_STRING];
            foreach ($tempParam as $index => $term) {
                if ($term === 'count' || $term === '0') {
                    $FHIRBundleTotal = $this->mapping->createSearchBundle();
                    $FHIRBundleTotal->setTotal($this->FHIRBundle->getTotal());
                    $FHIRBundle = $FHIRBundleTotal;
                    unset($this->params['PARAMETERS_FOR_SEARCH_RESULT']['_summary'][$index]);
                    $return = $FHIRBundle;
                }
            }
            if (empty($this->params['PARAMETERS_FOR_SEARCH_RESULT']['_summary'])) {
                unset($this->params['PARAMETERS_FOR_SEARCH_RESULT']['_summary']);
            }
        }
        return $return;

    }

    private function sortHandler($searchArg)
    {
        if (is_array($searchArg) && key_exists(self::SORT_STRING, $searchArg)) {
            $sortArr = $searchArg[self::SORT_STRING];
            foreach ($sortArr as $key => $val) {
                //convert request name to db column name
                $dbName = $this->mapping->convertFieldsToDB(array($val['value'] => "RETURN_DB_NAME"));
                if (!empty($dbName)) {
                    $this->allSorts[] = $dbName[$val['value']] . " " . $val['operator'];
                    if($dbName[$val['value']]==="pc_eventDate"){
                        $this->allSorts[] = "pc_startTime" . " " . $val['operator'];
                    }

                }
                unset($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::SORT_STRING][$key]);
            }
            if (empty($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::SORT_STRING])) {
                unset($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::SORT_STRING]);
            }
        }
        return true;
    }


    private function countHandler($searchArg)
    {
        if (is_array($searchArg) && key_exists(self::COUNT_STRING, $searchArg)) {

            $count=intval($searchArg[self::COUNT_STRING][0]);

            $this->filters['_count'] = $count;
            unset($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::COUNT_STRING][0]);


            if (empty($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::COUNT_STRING])) {
                unset($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::COUNT_STRING]);
            }
        }

        return true;
    }






    /**
     * create FHIRAppointment
     *
     * @param string
     * @param string
     * @param array
     * @return array | null
     * @throws
     */
    private function getAppointmentsByParams($fromDate = '0000-00-00', $toDate = '9999-12-31', $params = array(), $sorts = array() ) {

        $postcalendarEventsTable = $this->container->get(PostcalendarEventsTable::class);
        $appointment = $postcalendarEventsTable->getNoneRecurrent(null, $params, $fromDate, $toDate, $sorts);

        if (!is_array($appointment) || count($appointment) < 1) {
            return null;
        } else {
            $FHIRAppointmentArr = array();
            foreach ($appointment as $key => $element) {
                $this->mapping->initFhirObject();
                $FHIRAppointmentArr[] = $this->mapping->DBToFhir($element, true);
            }
            return $FHIRAppointmentArr;
        }
    }

    /**
     * returns empty bundle with total 0
     * or return bundle created by summaryHandler
     * if _summary is set
     *
     * @return FHIRBundle |
     */
    private function notFoundError()
    {
        $parametersForSearch = $this->params['PARAMETERS_FOR_SEARCH_RESULT'];
        $summary = $this->summaryHandler($parametersForSearch);
        if ($summary !== false) {
            return $summary;
        } else {
            $noResultBundle = $this->mapping->createSearchBundle();
            return $noResultBundle;
        }
    }

    private function badRequestError()
    {
        $code = '404';
        echo 'bad request params';
        http_response_code($code);exit();
    }

    private function includeHandler($searchArg)
    {
        if (is_array($searchArg) && key_exists(self::INCLUDE_STRING, $searchArg)) {
            $tempParam = $searchArg[self::INCLUDE_STRING];
            foreach ($tempParam as $index => $term) {
                if ($term === 'Appointment:patient') {
                    // include patient file
                    $this->FHIRBundle = $this->attachPatientsToAppointments($this->FHIRBundle);
                    unset($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::INCLUDE_STRING][$index]);
                }
            }
            if (empty($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::INCLUDE_STRING])) {
                unset($this->params['PARAMETERS_FOR_SEARCH_RESULT'][self::INCLUDE_STRING]);
            }
        }

        return true;
    }

    /**
     * attach patients ot appointments bundle
     *
     * @param FHIRBundle
     *
     * @return FHIRBundle | null
     * @throws
     */
    private function attachPatientsToAppointments(FHIRBundle $bundle)
    {
        $params = array(
            'paramsFromUrl' => $this->paramsFromUrl,
            'paramsFromBody' => $this->paramsFromBody,
            'container' => $this->container
        );

        $FhirPatientMapping = new Patient($params);

        $entries = $bundle->getEntry();
        $allPatients = array();

        foreach ($entries as $key => $entry) {

            $resource = $entry->getResource();
            if (is_null($resource)) {
                continue;
            }
            $resourceName = $resource->get_fhirElementName();


            if ($resourceName === 'Appointment') {
                $participants = $resource->getParticipant();

                foreach ($participants as $index => $participant) {
                    $actor = $participant->getActor();
                    $actorRef = $actor->getReference()->getValue();
                    $refParts = explode('/', $actorRef);
                    if ($refParts[0] === "Patient" && !in_array($refParts[1], $allPatients)) {
                        $pid = $refParts[1];
                        $allPatients[] = $pid;
                        $FhirPatientMapping->setParamsFromUrl(array($pid));

                        $FHIRPatient = $FhirPatientMapping->read();
                        $FHIRResourceContainer = new FHIRResourceContainer($FHIRPatient);
                        $bundle = $this->mapping->addResourceToBundle($bundle, $FHIRResourceContainer, 'include');
                    }

                }

            }
        }

        return $bundle;
    }

    private function idHandler($searchArg)
    {
        if (is_array($searchArg) && key_exists(self::ID_STRING, $searchArg)) {

            $eid=intval($searchArg[self::ID_STRING][0]);

            $this->filters['pc_eid'] = $eid;
            unset($this->params['PARAMETERS_FOR_ALL_RESOURCES'][self::ID_STRING][0]);


            if (empty($this->params['PARAMETERS_FOR_ALL_RESOURCES'][self::ID_STRING])) {
                unset($this->params['PARAMETERS_FOR_ALL_RESOURCES'][self::ID_STRING]);
            }
        }

        return true;
    }

}
