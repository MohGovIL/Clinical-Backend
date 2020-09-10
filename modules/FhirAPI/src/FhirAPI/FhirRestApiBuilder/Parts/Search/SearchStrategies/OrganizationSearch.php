<?php
/**
 * @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * Encounter search - strategy
 */
namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use OpenEMR\FHIR\R4\FHIRResourceContainer;

class OrganizationSearch extends BaseSearch
{

    public $paramsToDB = array();
    public $MAIN_TABLE = 'facility';
    public function search()
    {
        $this->paramHandler('_id','id');
        $this->paramHandler('name','name');
        $this->paramHandler('active','active');
        $this->paramHandler('type','pos_code');
        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;
    }

}
