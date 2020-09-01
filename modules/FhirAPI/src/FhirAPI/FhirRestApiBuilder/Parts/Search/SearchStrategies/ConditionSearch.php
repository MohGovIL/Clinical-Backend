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
        $this->paramHandler('encounter','encounter');

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
        $this->paramHandler('patient','pid');

        if(isset($this->searchParams['category'])){
            $catLinkVal=$this->searchParams['category'][0]['value'];
            $catArr=explode("|",substr($catLinkVal, strrpos($catLinkVal, '/') + 1));
            if(!empty($catArr)){
                $this->searchParams['category'][0]['value']=$catArr[0];
                if(!is_null($catArr[1])){
                    $specificCodeType= array (0 => array ('value' => $catArr[1], 'operator' => NULL, 'modifier' => 'exact'));
                    $this->searchParams['categoryCode'] =$specificCodeType;
                }
            }else{
                $configureType =  array (
                    0 => array ('value' => 'medical_problem', 'operator' => NULL, 'modifier' => 'exact'),
                    1 => array ('value' => 'allergy', 'operator' => NULL, 'modifier' => 'exact')
                );
                $this->searchParams['type'] =$configureType;
            }

        }

        $this->paramHandler('category','type');
        $this->paramHandler('categoryCode','list_option_id');

        $this->searchParams = $this->paramsToDB;

        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
