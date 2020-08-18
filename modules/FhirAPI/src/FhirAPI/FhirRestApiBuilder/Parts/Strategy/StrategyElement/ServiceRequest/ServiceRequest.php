<?php
/**
 * Date: 24/03/20
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Questionnaire
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ServiceRequest;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ServiceRequest\FhirServiceRequestMapping;

use FhirAPI\Model\FhirServiceRequestTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaire;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRServiceRequest;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class ServiceRequest Extends Restful implements  Strategy
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
        $this->mapping = new FhirServiceRequestMapping($container);
        $this->mapping->setSelfFHIRType($strategyName);
    }

    public function getFormQuestionMapping()
    {
        return $this->mapping->getFormQuestionMapping();
    }


/********************end of base internal functions********************************************************************/

    /**
     * create FHIRServiceRequest
     *
     * @param string
     * @return FHIRServiceRequest | FHIRBundle
     * @throws
     */
    public function read()
    {
        $fid=$this->paramsFromUrl[0];
        $fhirServiceRequestTable = $this->container->get(FhirServiceRequestTable::class);
        $ServiceRequest =$fhirServiceRequestTable->buildGenericSelect(["fhir_service_request.id"=>$fid]);

        if (!is_array($ServiceRequest) || count($ServiceRequest) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }
        $this->mapping->initFhirObject();
        $apt= $this->mapping->DBToFhir($ServiceRequest[0], true);
        $this->mapping->initFhirObject();
        return $apt;

    }

    /**
     * search FHIRServiceRequest
     *
     * @param string
     * @return FHIRBundle | null
     * @throws
     */
    public function search()
    {
        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(FhirServiceRequestTable::class),
            'fhirObj'=>new FhirServiceRequestMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'ServiceRequestSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();


    }

    /**
     * update ServiceRequest data
     *
     * @param string
     * @return FHIRBundle | FHIRServiceRequest
     * @throws
     */
    public function patch()
    {
        $initPatch['paramsFromUrl']=$this->paramsFromUrl;
        $initPatch['paramsFromBody']=$this->paramsFromBody;
        $initPatch['container']=$this->container;
        $initPatch['mapping']=$this->mapping;
        $initPatch['selfApiCalls']=new ServiceRequest($initPatch);
        $patch = new GenericPatch($initPatch);
        return $patch->patch();
    }

    /**
     * create ServiceRequest data
     *
     * @param string
     * @return FHIRBundle | FHIRServiceRequest
     * @throws
     */
    public function create()
    {
        $dbData = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);

        $serviceRequestTable = $this->container->get(FhirServiceRequestTable::class);

        $allData=array('new'=>$dbData,'old'=>array());
        //$mainTable=$formVitalsTable->getTableName();
        $isValid=$this->mapping->validateDb($allData,null);

        if($isValid){
            unset($dbData['id']);
            $rez=$serviceRequestTable->safeInsert($dbData,'id');
            if(is_array($rez)){
                $serviceRequest=$this->mapping->DBToFhir($rez);
                return $serviceRequest;
            }else{ //insert failed
                ErrorCodes::http_response_code('500','insert object failed :'.$rez);
            }
        }else{ // object is not valid
            ErrorCodes::http_response_code('406','failed validation');
        }
        //this never happens since ErrorCodes call to exit()
        return new FHIRServiceRequest;
    }

    /**
     * update ServiceRequest data
     *
     * @param string
     * @return FHIRBundle | FHIRServiceRequest
     * @throws
     */
    public function update()
    {
        $dbData = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        $srid =$this->paramsFromUrl[0];
        return $this->mapping->updateDbData($dbData,$srid);

     }


}
