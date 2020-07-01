<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use laminas\Db\Sql\Expression;
use laminas\Db\Sql\Select;

class ObservationSearch extends BaseSearch
{
    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'form_vitals';
    public function search()
    {
        $this->paramHandler('_id','id');
        $this->paramHandler('issued','outcome');
        $this->paramHandler('patient','pid');
        $this->paramHandler('performer','user');
        $this->paramHandler('status','activity');
        $this->paramHandler('encounter','eid');
        $this->paramHandler('category','category');

        $this->searchParams = $this->paramsToDB;

        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
