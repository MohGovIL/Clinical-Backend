<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class QuestionnaireResponseSearch extends BaseSearch
{
    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'questionnaire_response';


    // overwriting base class runMysqlQuery since need to run setQuestionnaire
    public function runMysqlQuery()
    {
        $dataFromDb = $this->searchThisTable->buildGenericSelect($this->searchParams, implode(",", $this->orderParams), array());
        foreach ($dataFromDb as $key => $data) {
            $this->fhirObj->setQuestionnaire($data['questionnaire_id']);
            $this->fhirObj->initFhirObject();
            $FHIRResourceContainer = new FHIRResourceContainer($this->fhirObj->DBToFhir($data));
            $this->FHIRBundle = $this->fhirObj->addResourceToBundle($this->FHIRBundle, $FHIRResourceContainer, 'match');
        }
    }


    public function search()
    {
        $args=$this->paramsFromBody['ARGUMENTS'];
        $this->paramHandler('_id','id');
        $this->paramHandler('status','status');
        $this->paramHandler('encounter','encounter');

        if(is_array($args['questionnaire'])){
            $this->paramHandler('questionnaire','questionnaire_id'); //questionnaire_id   reg.id
            $this->paramsToDB['reg.id'] = $this->paramsToDB[$this->MAIN_TABLE.'.questionnaire_id'];
            unset($this->paramsToDB[$this->MAIN_TABLE.'.questionnaire_id']);
            $this->paramsToDB[$this->MAIN_TABLE.'.subject_type']=array(array('value'=>'Patient','operator'=>null,'modifier'=>'exact'));
        }

        //$this->paramHandler('identifier','form_id');
        $this->paramHandler('subject','subject');
        $this->paramHandler('author','create_by');

        if(is_array($args['patient'])){
            $this->paramHandler('patient','subject');
            $this->paramsToDB[$this->MAIN_TABLE.'.subject_type']=array(array('value'=>'Patient','operator'=>null,'modifier'=>'exact'));
        }

        $this->searchParams = $this->paramsToDB;
        $this->runMysqlQuery();
        return $this->FHIRBundle;

    }

}
