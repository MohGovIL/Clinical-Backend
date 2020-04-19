<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\HealthcareService;

use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use GenericTools\Model\HealthcareServicesTable;
use OpenEMR\FHIR\R4\FHIRResourceContainer;

class HealthcareService Extends Restful implements  Strategy
{
    private $searchParams;

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
        $this->mapping = new FhirHealthcareServiceMapping($container);
    }

    private function getSearchParams()
    {
        return $this->searchParams;
    }

    private function addSearchParam($param, $value)
    {
        $this->searchParams[$param] = $value;
    }

    public function read()
    {
        $fhirHealthcareServiceMapping = $this->mapping;
        $healthcareServiceTable = $this->container->get(HealthcareServicesTable::class);
        $params = array('id' => $this->paramsFromUrl[0]);
        $healthcareServicesDataFromDb = $healthcareServiceTable->buildGenericSelect($params);

        if(empty($healthcareServicesDataFromDb))
        {
            //not found
            return self::$errorCodes::http_response_code(204);
        }

        $healthcareServiceDataFromDb = $healthcareServicesDataFromDb[0];
        $healthcareService = $fhirHealthcareServiceMapping->DBToFhir($healthcareServiceDataFromDb, []);

        $fhirHealthcareServiceMapping->setNewFHIRHealthcareService();

        return $healthcareService;
    }

    public function search()
    {

        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(HealthcareServicesTable::class),
            'fhirObj'=>new FhirHealthcareServiceMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'HealthcareServiceSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

    }

}
