<?php
/**
 * Date: 24/06/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Fhir CONDITION
 *
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MedicationRequest;
/*must have use*/
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
/*************/
use GenericTools\Model\ListsOpenEmrTable;
use GenericTools\Model\FormVitalsTable;
use GenericTools\Model\PrescriptionsTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRMedicationRequest;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class MedicationRequest Extends Restful implements  Strategy
{


    /********************base internal functions***************************************************************************/

    public function __construct($params=null)
    {
        if(!is_null($params))
        {
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
        $this->mapping = new FhirMedicationRequestMapping($container);
    }

    /********************end of base internal functions********************************************************************/

    /**
     * create FHIRMedicationRequest
     *
     * @return FHIRMedicationRequest
     * @throws
     */
    public function read()
    {
        $eid=$this->paramsFromUrl[0];

        $prescriptionsTable = $this->container->get(PrescriptionsTable::class);
        $medicationRequest =$prescriptionsTable->getDataByParams(array("id"=>intval($eid)));

        if (!is_array($medicationRequest) || count($medicationRequest) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }
        $this->mapping->initFhirObject();
        $md= $this->mapping->DBToFhir($medicationRequest[0], true);
        $this->mapping->initFhirObject();
        return $md;

    }

    /**
     * search in FHIRMedicationRequest elements
     *
     * @return FHIRBundle
     */
    public function search()
    {

        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(PrescriptionsTable::class),
            'fhirObj'=>new FhirMedicationRequestMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'MedicationRequestSearch'
        );

        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

    }

    public function create()
    {
        $dbData = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);

        $prescriptionsTable = $this->container->get(PrescriptionsTable::class);
        $flag=$this->mapping->validateDb($dbData);
        if($flag){
            unset($dbData['id']);
            $rez=$prescriptionsTable->safeInsert($dbData,'id');
            if(is_array($rez)){
                $medicationRequest=$this->mapping->DBToFhir($rez);
                return $medicationRequest;

            }else{ //insert failed
                ErrorCodes::http_response_code('500','insert object failed :'.$rez);
            }
        }else{ // object is not valid
            ErrorCodes::http_response_code('406','object is not valid');
        }
        //this never happens since ErrorCodes call to exit()
        return false;


    }

    public function update()
    {
        $dbData = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        $eid =$this->paramsFromUrl[0];
        return $this->mapping->updateDbData($dbData,$eid);
    }

    /**
     * update Prescriptions data
     *
     * @param string
     * @return FHIRBundle
     * @throws
     */
    public function patch()
    {
        $initPatch['paramsFromUrl']=$this->paramsFromUrl;
        $initPatch['paramsFromBody']=$this->paramsFromBody;
        $initPatch['container']=$this->container;
        $initPatch['mapping']=$this->mapping;
        $initPatch['selfApiCalls']=new MedicationRequest($initPatch);

        $patch = new GenericPatch($initPatch);
        return $patch->patch();
    }


    public function delete()
    {
        $id=$this->paramsFromUrl[0];
        $prescriptionsTable = $this->container->get(PrescriptionsTable::class);
        $delete=$prescriptionsTable->deleteDataByParams(array("id"=>$id));
        if($delete===1){
            return $this->mapping->createDeleteSuccessRespond();
        }else{
            $explanation="failed to delete from db ";
            return $this->mapping->createDeleteFailRespond($id,$explanation);
        }

    }

}
