<?php
/**
 * Created by Dror Golan 24/11/2019
 */

namespace GenericTools\Library\Alerts;

use Formhandler\Validator\ServerValidationHandler;
use GenericTools\Controller\BaseController;
use GenericTools\Model\ListsTable;
use GenericTools\Traits\magicMethods;
use Interop\Container\ContainerInterface;

/**
 * Class AbstractRules
 * @package GenericTools\Library\Alerts
 */
abstract class AbstractRules extends BaseController
{
    private $paramArrayToCheck;
    private $validationMatrix;
    public  $elements;
    public  $arrayOfElementsToCheck;
    public  $dataToCheckRuleOn;
    public $messageTranslated;

    /**
     * AbstractRules constructor.
     */
    public function __construct()
    {
        $this->paramArrayToCheck        =   null;
        $this->validationMatrix         =   null;
        $this->arrayOfElementsToCheck   =   null;
        $this->messageTranslated                  =   "";
        $this->incident_id = "";
    }

    /**
     * Add todays date to the db array
     */
    public function setTodayDate(){
        $this->dataToCheckRuleOn['today_date'] = date("Y-m-d", strtotime("now"));
    }

    /**
     * @param $required
     * @param $filters
     * @param $validations
     * @return array
     */
    public function buildValidationTicketArray($required,$filters,$validations){
        $validationTicket = array(
            'required'      =>      $required,
            'filters'       =>      $filters,
            'validations'   =>      $validations
        );
        return $validationTicket;
    }

    /**
     * @param bool $collectAllValidationForFurtherChecking
     * @return bool|mixed
     */
    public function isRuleMatch($collectAllValidationForFurtherChecking = false){
        if(is_null($this->paramArrayToCheck) || is_null($this->paramArrayToCheck))
            return false;

        if($collectAllValidationForFurtherChecking){
           return $this->validateElements($this->paramArrayToCheck, $this->serverSideValidation, [],$collectAllValidationForFurtherChecking);
        }
        else{

            $validatorhandler = $this->validateElements($this->paramArrayToCheck, $this->serverSideValidation, []);
        }

        if ($validatorhandler != "") {
            // $serverValidationRes=$validatorhandler->errorReduce($serverValidationRes);
            return false;
        }

        return true;
    }

    /**
     * @param $fieldName
     * @param $validation
     * @throws \Exception
     */
    public function addRuleToValidationMatrix($fieldName,$validation){

        //EXAMPLES OF  VALIDATORS
        /*
        ** $this->addValidation( 'offending_details_the_carcass_was_sent_for_review_on', false,[], [['GreaterThanDate',$this->getCreationDate() ,true]]);
        ** $this->addValidation( 'incident_details_incident_date', true,[], ['DateFromNow']);
        ** $this->addValidation( 'incident_details_incident_time', false,[], ['Time']);
        ** $this->addValidation( 'incident_details_description', true);
        */

        if($fieldName=='' || is_null($validation) || empty($validation))
        {
            throw new \InvalidArgumentException('"validation and fieldName" must contain data, validation  : '.gettype($validation).' , fieldName  : '.gettype($fieldName).' have been spotted.');
        }

        if(is_null($this->validationMatrix )){
            $this->validationMatrix =[];
        }

        $isRequired=$validation['isRequired'];
        $filters=$validation['filters'];
        $validations=$validation['validations'];
        $this->addValidation( $fieldName, $isRequired,$filters,$validations);

    }

    /**
     * @param $data
     * @return bool
     */
    public function checkForData($data){
        if(is_null($data))
            return false;

        if(empty($data))
            return false;

        return true;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param bool $checkForEmpty
     */
    public function addFieldsAndValuesToParamsArray($fieldName,$fieldValue,$checkForEmpty=false){
        if(!$checkForEmpty &&($fieldName=='' || is_null($fieldValue) ))
        {
            throw new \InvalidArgumentException('"fieldValue and fieldName" must contain data, validation  : '.gettype($fieldValue).' , fieldName  : '.gettype($fieldName).' have been spotted.');
        }

        if(is_null($this->paramArrayToCheck )){
            $this->paramArrayToCheck =[];
        }
        $this->paramArrayToCheck[$fieldName]=$fieldValue;
    }

    /**
     * @return |null
     */
    public function getValidationRulesMatrix(){
            return $this->validationMatrix;
    }

    /**
     * @return |null
     */
    public function getFieldsAndValuesToParamsArray(){
        return $this->paramArrayToCheck;
    }

    /**
     * @param $element
     * @return string
     */
    public function getValueOfElement($element){
       return  is_null($this->dataToCheckRuleOn[$element])?"":$this->dataToCheckRuleOn[$element];
    }

    /**
     * @param $element
     * @param $value
     */
    public function setValueToElement($element,$value){
        $this->dataToCheckRuleOn[$element]=$value;
    }

    /**
     * @return string
     */
    public function getIncidentDate(){
        return $this->getValueOfElement("incident_date");
    }

    /**
     * bindelements of this current rule for checking into param array for server validation
     */
    function bindElementsToArray(){
        foreach($this->elements as $element){
           // $this->arrayOfElementsToCheck[$element] = $this->dataToCheckRuleOn[$element];
            $this->addFieldsAndValuesToParamsArray($element,   $this->dataToCheckRuleOn[$element], true);
        }
    }

    /**
     * @return string
     */
    public function getIncidentId(){
        return $this->getValueOfElement("id");
    }
}
