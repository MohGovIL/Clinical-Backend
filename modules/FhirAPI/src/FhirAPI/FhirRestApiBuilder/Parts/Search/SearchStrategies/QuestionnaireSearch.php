<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;


class QuestionnaireSearch extends BaseSearch
{
    public $paramsToDB = array();
    public $MAIN_TABLE = 'fhir_questionnaire';
    public function search()
    {

        $this->paramHandler('title','name');
        $this->paramHandler('status','state',null,array("active"=>"1","retired"=>"0"));
        $this->paramHandler('name','directory');
        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
