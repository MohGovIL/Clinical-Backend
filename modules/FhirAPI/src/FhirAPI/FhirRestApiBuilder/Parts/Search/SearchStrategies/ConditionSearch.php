<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\ListsTable;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use laminas\Db\Sql\Expression;
use laminas\Db\Sql\Select;

class ConditionSearch extends BaseSearch
{
    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'lists';
    const OUTCOME_LIST ='outcome';
    public function search()
    {
        $this->paramHandler('_id','id');
        $this->paramHandler('active','active');
        $this->paramHandler('encounter','encounter','ie');

        if (isset($this->searchParams['clinical-status'])) {
            $code = $this->searchParams['clinical-status'][0]['value'];

            // to support search by status string
            if (!ctype_digit($code)) {
                $ListsTable = $this->container->get(ListsTable::class);
                $listOutcome = array_flip($ListsTable->getListNormalized(self::OUTCOME_LIST,null, null, null, false)); // not translated
                $code = $listOutcome[$code];
                if (!is_null($code)) {
                    $this->searchParams['clinical-status'][0]['value'] = $code;
                } else {
                    unset($this->searchParams['clinical-status']);
                }
            }
        }
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
