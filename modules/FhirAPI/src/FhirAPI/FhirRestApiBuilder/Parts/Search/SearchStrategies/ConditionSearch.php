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

        if(isset($this->searchParams['code:of-type'])){
            $codeSearch=$this->searchParams['code:of-type'][0]['value'];     // format |system|code|identifier
            $codeSearchArr=explode('|',$codeSearch);
            if(count($codeSearchArr)>2){
                $codeSearch=$codeSearchArr[1].':'.$codeSearchArr[2];
                $this->searchParams['code:of-type'][0]['value']=$codeSearch;
            }
            $this->paramHandler('code:of-type','diagnosis');
        }

        $this->paramHandler('subject','pid');
        $this->searchParams = $this->paramsToDB;

        $configureType =  array (
            0 => array ('value' => 'medical_problem', 'operator' => NULL, 'modifier' => 'exact',),
            1 => array ('value' => 'allergy', 'operator' => NULL, 'modifier' => 'exact',)
        );
        $this->searchParams['type'] =$configureType;
        $this->paramHandler('type','type');
        
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
