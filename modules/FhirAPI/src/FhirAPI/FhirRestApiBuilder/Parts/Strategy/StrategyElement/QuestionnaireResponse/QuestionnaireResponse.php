<?php
/**
 * Date: 24/03/20
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Fhir  QuestionnaireResponse
 *
 *
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\QuestionnaireResponse;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\QuestionnaireResponse\FhirQuestionnaireResponseMapping;
use FhirAPI\Model\QuestionnaireResponseTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaireResponse;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;

class QuestionnaireResponse Extends Restful implements  Strategy
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
        $this->mapping = new FhirQuestionnaireResponseMapping($container);
    }

/********************end of base internal functions********************************************************************/

    /**
     * create FHIRQuestionnaireResponse
     *
     * @param string
     * @return FHIRQuestionnaireResponse | FHIRBundle
     * @throws
     */
    public function read()
    {
        $qrid=$this->paramsFromUrl[0];
        $questionnaireResponseTable = $this->container->get(QuestionnaireResponseTable::class);
        $questionnaireResponse=$questionnaireResponseTable->buildGenericSelect(["questionnaire_response.id"=>$qrid]);


        if (!is_array($questionnaireResponse) || count($questionnaireResponse) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }

        $questionnaireId=$questionnaireResponse[0]['questionnaire_id'];
        if(is_null($questionnaireId)){
            return ErrorCodes::http_response_code('412','Precondition Failed');
        }
        $this->mapping->setQuestionnaire($questionnaireId);


        $this->mapping->initFhirObject();
        $apt= $this->mapping->DBToFhir($questionnaireResponse[0], true);
        $this->mapping->initFhirObject();
        return $apt;

    }

    /**
     * search FHIRQuestionnaireResponse
     *
     * @param string
     * @return FHIRBundle | null
     * @throws
     */
    public function search()
    {
        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(QuestionnaireResponseTable::class),
            'fhirObj'=>$this->mapping,
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'QuestionnaireResponseSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();
    }

    /**
     * update QuestionnaireResponse data
     *
     * @param string
     * @return FHIRBundle | FHIRQuestionnaireResponse
     * @throws
     */
    public function patch()
    {
        return ErrorCodes::http_response_code('405','Method Not Allowed');
    }

    /**
     * create QuestionnaireResponse data
     *
     * @param string
     * @return FHIRBundle | FHIRQuestionnaireResponse
     * @throws
     */
    public function create()
    {
        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        unset($dBdata['questionnaire_response']['id']);
        $dBdata['questionnaire_response']['create_by']=$dBdata['questionnaire_response']['update_by'];
        $dBdata['questionnaire_response']['create_date']=date('Y-m-d H:i:s');
        $dBdata['questionnaire_response']['update_date']=date('Y-m-d H:i:s');

        $questionnaireResponseTable = $this->container->get(QuestionnaireResponseTable::class);
        $inserted=$questionnaireResponseTable->safeInsert($dBdata['questionnaire_response'],'id');
        $formId=$this->mapping->saveAnswers($dBdata,$inserted);

        $this->paramsFromUrl[0]=$formId;
        return $this->read();
    }

    /**
     * update QuestionnaireResponse data
     *
     * @param string
     * @return FHIRBundle | FHIRQuestionnaireResponse
     * @throws
     */
    public function update()
    {

        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        $dBdata['questionnaire_response']['update_date']=date('Y-m-d H:i:s');
        $questionnaireResponseTable = $this->container->get(QuestionnaireResponseTable::class);
        $primaryKey='id';
        $id=$dBdata['questionnaire_response']['id'];

        $updated=$questionnaireResponseTable->safeUpdate($dBdata['questionnaire_response'],array($primaryKey=>$id));
        $formId=$this->mapping->saveAnswers($dBdata,$updated);

        $this->paramsFromUrl[0]=$formId;
        return $this->read();
     }


}
