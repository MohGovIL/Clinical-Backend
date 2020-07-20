<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;


class ServiceRequestSearch extends BaseSearch
{
    public $paramsToDB = array();
    public $MAIN_TABLE = 'fhir_service_request';
    public function search()
    {

        if(!empty($this->searchParams)){
            foreach($this->searchParams as $index => $data){
                if($index === "occurrence" || $index === "authored"){
                    foreach($data as $pos => $record){
                        // notice this search support only date time for now
                        $this->searchParams[$index][$pos]['value']=$this->fhirObj->convertToDateTime($record['value']);

                    }
                }
            }
        }

        $this->paramHandler('_id','id');
        $this->paramHandler('encounter','encounter');
        $this->paramHandler('patient','patient');
        $this->paramHandler('authored','authored_on');
        $this->paramHandler('occurrence','occurrence_datetime');
        $this->paramHandler('status','status');

        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
