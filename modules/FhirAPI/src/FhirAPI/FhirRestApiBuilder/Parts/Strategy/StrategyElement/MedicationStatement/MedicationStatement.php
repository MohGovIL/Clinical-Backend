<?php
/**
 * Date: 07/06/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Fhir CONDITION
 *
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MedicationStatement;
/*must have use*/
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
/*************/

use GenericTools\Model\IssueEncounterTable;
use GenericTools\Model\ListsOpenEmrTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRPatient;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class MedicationStatement Extends Restful implements  Strategy
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
        $this->setMapping($initials['container'],$initials['strategyName']);

    }

    public function doAlgorithm($arrParams)
    {
        $this->initParams($arrParams);

        $this->functionName = $arrParams['type'];
        $function = Restful::$data[$arrParams['strategyName']][self::$function][$this->functionName];
        return $this->$function();
    }

    public function setMapping($container,$strategyName)
    {
        $this->mapping = new FhirMedicationStatementMapping($container);
        $this->mapping->setSelfFHIRType($strategyName);
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
        $medicationStatement =$listsOpenEmrTable->buildGenericSelect(array("id"=>intval($eid)));

        if (!is_array($medicationStatement) || count($medicationStatement) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }
        $this->mapping->initFhirObject();
        $apt= $this->mapping->DBToFhir($medicationStatement[0], true);
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
            'fhirObj'=>new FhirMedicationStatementMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'MedicationStatementSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

    }

    public function create()
    {
        $dbData = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);

        $listsOpenEmrTable = $this->container->get(ListsOpenEmrTable::class);
        /*********************************** validate *******************************/
        $allData=array('new'=>$dbData,'old'=>array());
        //$mainTable=$listsOpenEmrTable->getTableName();
        $isValid=$this->mapping->validateDb($allData,null);
        /***************************************************************************/

        if($isValid){
            unset($dbData['lists']['id']);
            $dbData['lists']['reaction']= is_null($dbData['lists']['reaction']) ? "" : $dbData['lists']['reaction'];
            $rez=$listsOpenEmrTable->safeInsert($dbData['lists'],'id');

            if(is_array($rez)){
                if(!empty($dbData['issue_encounter']['encounter'])){
                    $dbData['issue_encounter']['list_id']= $rez['id'];
                    $issueEncounterTable = $this->container->get(IssueEncounterTable::class);

                    // safe insert only support insert by single primary key
                    $rez2=$issueEncounterTable->insert($dbData['issue_encounter']);

                    if($rez2!==1){
                        //todo : call delete by $dbData['lists']['id']
                        ErrorCodes::http_response_code('500','insert encounter info failed :'.$rez);
                    }else{
                        $rez['encounter'] =  $dbData['issue_encounter']['encounter'];
                    }
                }

                $this->mapping->initFhirObject();  // this is very important to clean old data

                $medicationStatement=$this->mapping->DBToFhir($rez);

                return $medicationStatement;
            }else{ //insert failed
                ErrorCodes::http_response_code('500','insert object failed :'.$rez);
            }
        }else{ // object is not valid
            ErrorCodes::http_response_code('406','failed validation');
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
        $initPatch['selfApiCalls']=new MedicationStatement($initPatch);

        $patch = new GenericPatch($initPatch);
        return $patch->patch();
    }

}
