<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class strategy Fhir  Encounter
 *
 *
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Encounter;

use FhirAPI\FhirRestApiBuilder\Parts\Patch\GenericPatch;
use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;

use GenericTools\Model\FormEncounterTable;
use Interop\Container\ContainerInterface;

/*include FHIR*/

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIREncounter;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;


class Encounter Extends Restful implements  Strategy
{
    const SEARCHSTRATEGYPATH = "FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies\\";
    const LEFTJOIN = "JOIN_LEFT";
    const RIGHTJOIN = "RIGHT_LEFT";
    const JOININNER = "INNER_JOIN";


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


    private function setMapping($container)
    {
        $this->mapping = new FhirEncounterMapping($container);
    }

    public function getJoinArray(){

    }

    public function read()
    {
        $fhirEncounterMapping =$this->mapping;
        $encounterTable = $this->container->get(FormEncounterTable::class);
        $encounterDataFromDb = $encounterTable->buildGenericSelect(["id"=>$this->paramsFromUrl[0]]);
        if(!$encounterDataFromDb)
        {
            //not found
            return self::$errorCodes::http_response_code(204);
        }

        $this->mapping->initFhirObject();
        $fhirEncounter=$fhirEncounterMapping->DBToFhir($encounterDataFromDb[0],[]);
        $this->mapping->initFhirObject();
        return $fhirEncounter;
    }


    public function search()
    {
        // TODO: Implement Search() method.
        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(FormEncounterTable::class),
            'fhirObj'=>new FhirEncounterMapping($this->container),
            'paramsToSearch'=>Registry::getSearchParamsAvailibleForThisStrategy("Encounter"),
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl[0],
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'EncounterSearch',
            'join' =>  null
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

        //return SearchContext::search($paramsToSearch);

    }

    /**
     * create Encounter
     *
     * @param string
     * @return FHIRBundle | FHIREncounter
     * @throws
     */
    public function create()
    {
        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        unset($dBdata['form_encounter']['id']);
        $formEncounterTable = $this->container->get(FormEncounterTable::class);
        $inserted=$formEncounterTable->safeInsertEncounter($dBdata);
        $this->paramsFromUrl[0]=$inserted[0]['id'];
        return $this-> read();


    }


    /**
     * update Encounter data
     *
     * @param string
     * @return FHIRBundle | FHIREncounter
     * @throws
     */
    public function update()
    {
        $dBdata = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        $id =$this->paramsFromUrl[0];
        return $this->mapping->updateDbData($dBdata,$id);

    }


    /**
     * update Encounter data
     *
     * @param string
     * @return FHIRBundle | FHIREncounter
     * @throws
     */
    public function patch()
    {
        $initPatch['paramsFromUrl']=$this->paramsFromUrl;
        $initPatch['paramsFromBody']=$this->paramsFromBody;
        $initPatch['container']=$this->container;
        $initPatch['mapping']=$this->mapping;
        $initPatch['selfApiCalls']=new Encounter($initPatch);

        $patch = new GenericPatch($initPatch);
        return $patch->patch();
    }



    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;

    }

    /**
     * @param mixed $params
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }




}
