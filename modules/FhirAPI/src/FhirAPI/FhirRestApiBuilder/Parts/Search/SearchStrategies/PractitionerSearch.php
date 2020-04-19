<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;


class PractitionerSearch extends BaseSearch
{
    public $paramsToDB = array();
    public $MAIN_TABLE = 'users';
    public function search()
    {

        $this->paramHandler('_id','id');
        $this->paramHandler('given','fname');
        $this->paramHandler('family','lname');
        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
