<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use laminas\Db\Sql\Expression;
use laminas\Db\Sql\Select;

class ConditionSearch extends BaseSearch
{
    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'lists';
    public function search()
    {
        $this->paramHandler('_id','id');
        $this->paramHandler('active','active');
        $this->paramHandler('clinical-status','outcome');
        $this->paramHandler('code','diagnosis');
        $this->paramHandler('subject','pid');
        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
