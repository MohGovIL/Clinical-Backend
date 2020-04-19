<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;


class QuestionnaireSearch extends BaseSearch
{
    public $paramsToDB = array();
    public $MAIN_TABLE = 'registry';
    public function search()
    {

        $this->paramHandler('title','name');
        $this->paramHandler('status','state');
        $this->paramHandler('name','directory');
        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
