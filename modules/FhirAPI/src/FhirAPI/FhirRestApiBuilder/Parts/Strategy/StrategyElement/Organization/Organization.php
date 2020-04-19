<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class strategy Fhir  ORGANIZATION
 *
 *
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Organization;

use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;

use GenericTools\Model\FacilityTable;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Organization\FhirOrganizationMapping;
use Interop\Container\ContainerInterface;

/*include FHIR*/

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIROrganization;

use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;


class Organization Extends Restful implements  Strategy
{

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
        $this->mapping = new FHIROrganizationMapping($container);
    }

    public function read()
    {
        $fhirOrganizationMapping =$this->mapping;
        $facilityTable = $this->container->get(FacilityTable::class);
        $facilityDataFromDb = $facilityTable->getFacility($this->paramsFromUrl[0]);
        if(!$facilityDataFromDb)
        {
            //not found
            return self::$errorCodes::http_response_code(204);
        }
        return $fhirOrganizationMapping->DBToFhir($facilityDataFromDb,[]);

    }


    public function search()
    {
        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(FacilityTable::class),
            'fhirObj'=>new FHIROrganizationMapping($this->container),
            'paramsToSearch'=>Registry::getSearchParamsAvailibleForThisStrategy("Organization"),
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl[0],
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'OrganizationSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

        //return SearchContext::search($paramsToSearch);

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

}
