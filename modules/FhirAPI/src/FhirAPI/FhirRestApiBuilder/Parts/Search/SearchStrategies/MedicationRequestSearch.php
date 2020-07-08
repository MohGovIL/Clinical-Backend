<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\ListsTable;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use laminas\Db\Sql\Expression;
use laminas\Db\Sql\Select;

class MedicationRequestSearch extends BaseSearch
{

    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'prescriptions';

    public function search()
    {
        $this->paramHandler('_id', 'id');
        $this->paramHandler('encounter', 'encounter');
        $this->paramHandler('patient', 'patient_id');
        $this->paramHandler('recorder', 'provider_id');
        $this->paramHandler('requester', 'user');
        $this->paramHandler('code', 'drug_id');

        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
