<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;


class RelatedPersonSearch extends BaseSearch
{
    public $paramsToDB = array();
    public $MAIN_TABLE = 'related_person';
    public function search()
    {

        $this->paramHandler('_id','id');
        $this->paramHandler('identifier','identifier');
        $this->paramHandler('active','active');
        $this->paramHandler('patient','pid');
        $this->paramHandler('relationship','relationship');
        $this->paramHandler('gender','gender');
        $this->paramHandler('email','email');
        $this->paramHandler('name','full_name');
        // search by phone means search by any telecom that has system='phone'
        //that means we need to map both phone_home and phone_cell to phone
        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
