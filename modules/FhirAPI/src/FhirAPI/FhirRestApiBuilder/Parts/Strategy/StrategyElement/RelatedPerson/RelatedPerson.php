<?php
/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Fhir  ORGANIZATION
 *
 *
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\RelatedPerson;

use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\RelatedPerson\FhirRelatedPersonMapping;




use GenericTools\Model\RelatedPersonTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRRelatedPerson;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;



class RelatedPerson Extends Restful implements  Strategy
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
        $this->mapping = new FhirRelatedPersonMapping($container);
    }

/********************end of base internal functions********************************************************************/

    /**
     * create FHIRRelatedPerson
     *
     * @param string
     * @return FHIRRelatedPerson | FHIRBundle
     * @throws
     */
    public function read()
    {
        $eid=$this->paramsFromUrl[0];
        $postcalendarEventsTable = $this->container->get(RelatedPersonTable::class);
        $relatedPerson =$postcalendarEventsTable->getDataByParams(array("id"=>intval($eid)));

        if (!is_array($relatedPerson) || count($relatedPerson) != 1) {
            $FHIRBundle = new FHIRBundle;
            $code='404';
            $error=array(0=>array('code'=>'404','text'=>'record was not found'));
            $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, array(),$error,$code);
            return $errorBundle;
        }
        $this->mapping->initFhirObject();
        $apt= $this->mapping->DBToFhir($relatedPerson[0], true);
        $this->mapping->initFhirObject();
        return $apt;

    }

    /**
     * search FHIRRelatedPerson
     *
     * @param string
     * @return FHIRBundle | null
     * @throws
     */
    public function search()
    {

        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(RelatedPersonTable::class),
            'fhirObj'=>$this->mapping,
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'RelatedPersonSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();



    }

    /**
     * update RelatedPerson data
     *
     * @param string
     * @return FHIRBundle | FHIRRelatedPerson
     * @throws
     */
    public function patch()
    {

        $initPatch['paramsFromUrl']=$this->paramsFromUrl;
        $initPatch['paramsFromBody']=$this->paramsFromBody;
        $initPatch['container']=$this->container;
        $initPatch['mapping']=$this->mapping;
        $initPatch['selfApiCalls']=new RelatedPerson($initPatch);
        $patch = new GenericPatch($initPatch);
        return $patch->patch();

    }

    /**
     * create RelatedPerson data
     *
     * @param string
     * @return FHIRBundle | FHIRRelatedPerson
     * @throws
     */
    public function create()
    {
        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        unset($dBdata['related_person']['id']);
        $relatedPersonTable = $this->container->get(RelatedPersonTable::class);
        $inserted=$relatedPersonTable->safeInsert($dBdata['related_person'],'id');
        return $this->mapping->DBToFhir($inserted, true);
    }

    /**
     * update RelatedPerson data
     *
     * @param string
     * @return FHIRBundle | FHIRRelatedPerson
     * @throws
     */
    public function update()
    {
        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        $id =$this->paramsFromUrl[0];
        return $this->mapping->updateDbData($dBdata,$id);

     }


}
