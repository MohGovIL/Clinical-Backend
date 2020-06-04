<?php
/**
 * Date: 29/01/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Fhir CONDITION
 *
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Condition;
/*must have use*/
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Patient\FhirPatientMapping;
/*************/

use GenericTools\Model\ListsOpenEmrTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRPatient;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class Condition Extends Restful implements  Strategy
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
        $this->mapping = new FhirConditionMapping($container);
    }

    /********************end of base internal functions********************************************************************/

    /**
     * create FHIRPatient
     *
     * @param  string
     * @return FHIRPatient
     * @throws
     */
    public function read()
    {
        $eid=$this->paramsFromUrl[0];
        $listsOpenEmrTable = $this->container->get(ListsOpenEmrTable::class);
        $condition =$listsOpenEmrTable->getDataByParams(array("id"=>intval($eid)));

        if (!is_array($condition) || count($condition) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }
        $this->mapping->initFhirObject();
        $apt= $this->mapping->DBToFhir($condition[0], true);
        $this->mapping->initFhirObject();
        return $apt;

    }

    /**
     * set FHIRAddress element
     *
     *
     * @return FHIRBundle
     */
    public function search()
    {

        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(ListsOpenEmrTable::class),
            'fhirObj'=>new FhirConditionMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'ConditionSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

    }

    public function create()
    {

        $dbData = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);

        $listsOpenEmrTable = $this->container->get(ListsOpenEmrTable::class);
        $flag=$this->mapping->validateDb($dbData);
        if($flag){
            unset($dbData['id']);
            $dbData['reaction']= is_null($dbData['reaction']) ? "" : $dbData['reaction'];
            $rez=$listsOpenEmrTable->safeInsert($dbData,'id');
            if(is_array($rez)){
                $patient=$this->mapping->DBToFhir($rez);
                return $patient;
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
     * update Appointment data
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
        $initPatch['selfApiCalls']=new Condition($initPatch);

        $patch = new GenericPatch($initPatch);
        return $patch->patch();
    }

}
