<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
/**
 * @author Dror Golan drorgo@matrix.co.il
 * interface for the strategies implementations
 */
interface SearchInt
{

    public function checkIfParamsAllowed();
    public function runExtendedParmSearch();
    public function convertParamsToDbParams();
    public function basicSearch();
    public function search();
    public function checkForSpecificSearch();
    public function runSimpleParamsSearch();

}
