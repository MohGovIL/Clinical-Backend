<?php
/**
 * @author Dror Golan drorgo@matrix.co.il
 * The strategy context that runs the search
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Search;


use FhirAPI\Service\FhirBaseMapping;
use FhirAPI\Service\FhirRequestParamsHandler;
use GenericTools\Model\PatientsTable;
use Mpdf\Shaper\Sea;
use OpenEMR\FHIR\R4\FHIRResourceContainer;


class SearchContext
{
    private $searchParams;

    public function __construct($searchParams){
        $this->searchParams = $searchParams;
    }
    public function doSearch(){
        $useStrsategy = new  $this->searchParams['buildThisSearch']($this->searchParams);
        return $useStrsategy->search();
    }

}


