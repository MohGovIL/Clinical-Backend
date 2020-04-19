<?php
/**
 * Date: 24/03/20
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Questionnaire
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Questionnaire;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Questionnaire\FhirQuestionnaireMapping;

use GenericTools\Model\RegistryTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaire;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRRelatedPerson;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class Questionnaire Extends Restful implements  Strategy
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
        $this->mapping = new FhirQuestionnaireMapping($container);
    }

    public function getFormQuestionMapping()
    {
        return $this->mapping->getFormQuestionMapping();
    }


/********************end of base internal functions********************************************************************/

    /**
     * create FHIRQuestionnaire
     *
     * @param string
     * @return FHIRQuestionnaire | FHIRBundle
     * @throws
     */
    public function read()
    {
        $fid=$this->paramsFromUrl[0];
        $registryTable = $this->container->get(RegistryTable::class);
        $questionnaire =$registryTable->buildGenericSelect(["registry.id"=>$fid]);

        if (!is_array($questionnaire) || count($questionnaire) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }
        $this->mapping->initFhirObject();
        $apt= $this->mapping->DBToFhir($questionnaire[0], true);
        $this->mapping->initFhirObject();
        return $apt;

    }

    /**
     * search FHIRQuestionnaire
     *
     * @param string
     * @return FHIRBundle | null
     * @throws
     */
    public function search()
    {
        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(RegistryTable::class),
            'fhirObj'=>new FhirQuestionnaireMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'QuestionnaireSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

    }

    /**
     * update Questionnaire data
     *
     * @param string
     * @return FHIRBundle | FHIRQuestionnaire
     * @throws
     */
    public function patch()
    {
        return ErrorCodes::http_response_code('405','Method Not Allowed');
    }

    /**
     * create Questionnaire data
     *
     * @param string
     * @return FHIRBundle | FHIRQuestionnaire
     * @throws
     */
    public function create()
    {
        return ErrorCodes::http_response_code('405','Method Not Allowed');
    }

    /**
     * update Questionnaire data
     *
     * @param string
     * @return FHIRBundle | FHIRQuestionnaire
     * @throws
     */
    public function update()
    {
        return ErrorCodes::http_response_code('405','Method Not Allowed');

     }


}
