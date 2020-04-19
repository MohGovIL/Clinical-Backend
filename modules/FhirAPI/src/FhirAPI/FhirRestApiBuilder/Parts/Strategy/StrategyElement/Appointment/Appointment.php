<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class strategy Fhir  ORGANIZATION
 *
 *
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Appointment;
/*must have use*/
use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Appointment\FhirAppointmentMapping;
/*************/

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Patient\Patient;
use GenericTools\Model\EventCodeReasonMapTable;
use GenericTools\Model\PostcalendarEventsTable;
use GenericTools\Model\HealthcareServicesTable;




/* include FHIR */
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRAppointment;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
/*************/

require_once($GLOBALS['srcdir'] . '/appointments.inc.php');

class Appointment Extends Restful implements  Strategy
{

/********************base internal functions***************************************************************************/

    public function __construct($params = null)
    {
        if (!is_null($params)) {
            $this->initParams($params);
        }
    }

    private function initParams($initials){
        $this->setParamsFromUrl($initials['paramsFromUrl']);
        $this->setParamsFromBody($initials['paramsFromBody']);
        $this->setContainer($initials['container']);
        $this->setMapping($initials['container']);
    }

    public function doAlgorithm($arrParams)
    {
        $this->initParams($arrParams);
        $this->functionName = $arrParams['type'];
        $function = Restful::$data[$arrParams['strategyName']][self::$function][$this->functionName];
        return $this->$function();
    }


    public function setMapping($container)
    {
        $this->mapping = new FhirAppointmentMapping($container);
    }

/********************end of base internal functions********************************************************************/

    /**
     * create FHIRAppointment
     *
     * @param string
     * @return FHIRAppointment | FHIRBundle
     * @throws
     */
    public function read()
    {
        $eid=$this->paramsFromUrl[0];
        $postcalendarEventsTable = $this->container->get(PostcalendarEventsTable::class);
        $appointment =$postcalendarEventsTable->getNoneRecurrent($eid);

        if (!is_array($appointment) || count($appointment) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }
        $this->mapping->initFhirObject();
        $apt= $this->mapping->DBToFhir($appointment[0], true);
        $this->mapping->initFhirObject();
        return $apt;

    }

    /**
     * search FHIRAppointment
     *
     * @param string
     * @return FHIRBundle | null
     * @throws
     */
    public function search()
    {
        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(PostcalendarEventsTable::class),
            'fhirObj'=>$this->mapping,
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'AppointmentSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();


    }

    /**
     * update Appointment data
     *
     * @param string
     * @return FHIRBundle | FHIRAppointment
     * @throws
     */
    public function patch()
    {
        $initPatch['paramsFromUrl']=$this->paramsFromUrl;
        $initPatch['paramsFromBody']=$this->paramsFromBody;
        $initPatch['container']=$this->container;
        $initPatch['mapping']=$this->mapping;
        $initPatch['selfApiCalls']=new Appointment($initPatch);

        $patch = new GenericPatch($initPatch);
        return $patch->patch();
    }

    /**
     * update Appointment data
     *
     * @param string
     * @return FHIRBundle | FHIRAppointment
     * @throws
     */
    public function create()
    {
        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        unset($dBdata['openemr_postcalendar_events']['pc_eid']);
        $dBdata['openemr_postcalendar_events']['pc_time']=date('Y-m-d H:i:s');
        $postcalendarEventsTable = $this->container->get(PostcalendarEventsTable::class);
        $inserted=$postcalendarEventsTable->safeInsertApt($dBdata);
        return $this->mapping->DBToFhir($inserted[0], true);
    }


    public function update()
    {
        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        $eid =$this->paramsFromUrl[0];
        return $this->mapping->updateDbData($dBdata,$eid);

     }


}
