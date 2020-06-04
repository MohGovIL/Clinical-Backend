<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

class HealthcareServiceSearch extends BaseSearch
{
    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'fhir_healthcare_services';
    public function search()
    {

        $this->paramHandler('_id','id');
        $this->paramHandler('active','active');
        $this->paramHandler('service-type','type');
        $this->paramHandler('organization','providedBy');
        $this->paramHandler('name','name');
        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
