<?php
/**
 * Created by Eyal Wolanowksi.
 * Date: 9/5/16 last updated 9/10/16
 * version 1.0
 * class that preforms custom Zend Validation
 * All validator classes should be in Formhandler\Validator
 */

namespace Formhandler\Validator;

use Zend\Validator;
use Zend\Validator\ValidatorChain;

class ServerValidationHandler
{
    const NAME="name";
    const CUSTOMVALIDATORPATH="Formhandler\\Validator\\";
    const ZENDVALIDATORPATH="Zend\\Validator\\";
    const ZENDFILTERPATH="Zend\\Filter\\";
    const GOOD="OK";
    const NOTFOUND="Validator not found";

    protected $formfields;
    protected $fieldstovalidate;
    protected $valid;
    protected $logArr;

    public function __construct(array $fields,array $matrix )
    {
      $this->formfields=$fields;
      $this->fieldstovalidate=$matrix;
      $this-> valid=TRUE;
      $this-> logArr=[];
    }

    /*
     *  return the validation result
     *  important !!! default value is TRUE
     */
    public function checkResult()
    {
      return $this-> valid;
    }

    public function getAnonymosFunctionsForEquality()
    {
        $farr = [];
        $farr ['eq'] = create_function('$x,$y', 'return $x==$y;');
        $farr ['checked'] = create_function('$x,$y', 'return $x==$y || $x=true;');
        $farr ['lt'] = create_function('$x,$y', 'return $x<$y;');
        $farr ['gt'] = create_function('$x,$y', 'return $x>$y;');
        $farr ['lte'] = create_function('$x,$y', 'return $x<=$y;');
        $farr ['gte'] = create_function('$x,$y', 'return $x>=$y;');
        return $farr;
    }




    /*
     * function to preform the validation
     */
    public function isValid()
    {
        $fields=$this->formfields;
        $matrix=$this->fieldstovalidate;
        /*loop 1*/
        foreach ($matrix as  $fieldName => $fieldValidatorsArr  ) {
         /*loop 2*/
            foreach ($fieldValidatorsArr as $validatorPos => $validatorArr) {

                $fieldValue=$fields[$fieldName];
                $validatorName=(is_array($validatorArr[self::NAME])) ? $validatorArr[self::NAME][0] : $validatorArr[self::NAME];           // The name might contain params to the validator

                $validatorNameArr=explode("(",$validatorName);
                $validatorParams=rtrim($validatorNameArr[1], ")");  //For Zend validators get the parms if exist
                $validatorName=ucfirst($validatorNameArr[0]);                //Extracting the validator name
                switch($validatorName){//all the custom validators must be with uppercase
                    case 'Int':
                        $validatorName = 'Digits';
                    break;
                    case 'Numeric':
                        $validatorName = 'NUM';
                        break;
                    case 'RequiredBy':
                        $fieldNameTemp = explode(",",str_ireplace("\"","",$validatorParams));
                        $fieldNameTemp =$fieldNameTemp [0];
                    break;
                    default:
                    break;
                }



                if($validatorName=='RequiredBy')
                {
                    $fieldNameTemp2=$fieldValue;
                    $fieldValue=$fieldNameTemp;
                }

                $farr =$this->getAnonymosFunctionsForEquality();

                if($validatorName=='SmallerThanDate' ){
                    $validatorParams=[$validatorArr['name'][1],$fieldName,isset($validatorArr['name'][2])?$validatorArr['name'][2]:false];
                }

                if($validatorName=='GreaterThanDate' ){
                    $validatorParams=[$validatorArr['name'][1],$fieldName,isset($validatorArr['name'][2])?$validatorArr['name'][2]:false];
                }

                if($validatorName == 'ValueIsEqualTo'){
                    $validatorParams=[$validatorArr['name'][1],$fieldName,isset($validatorArr['name'][2])?$validatorArr['name'][2]:false];
                }

                if($validatorName=='BetweenDates' ){
                    $validatorParams=[$validatorArr['name'][1],$fieldName,isset($validatorArr['name'][2])?$validatorArr['name'][2]:false];
                }

                if($validatorName=='RequireIf')
                {
                    $action = $validatorArr["rules"];
                    $checkIf = strtolower($action[0]);
                    $sign = $action[1];
                    $value = $action[2];

                    if($farr[$sign]($fields[$checkIf],$value))
                    {
                        $validatorName ='Required';
                        $fieldValidatorsArr['Required'] =true;
                    }
                    else{
                        $logArr=[];
                        return $logArr;
                    }

                }


                if((!isset($fieldValidatorsArr['required']) && empty($fieldValue)) &&  (!isset($fieldValidatorsArr[0]['name']) && $fieldValidatorsArr[0]['name'] !='required'  && empty($fieldValue) ))continue;

                if($validatorName=='RequiredBy')
                {

                    $fieldValue=$fieldNameTemp2;
                }




                //not checking validation if is not required and is empty
               // if(!isset($fieldValidatorsArr['required']) && empty($fieldValue))continue;

                // check if validator is in custom validation lib
                if (class_exists(self::CUSTOMVALIDATORPATH.$validatorName)) {
                    $classname = self::CUSTOMVALIDATORPATH . $validatorName;
                }
                else {
                    // check if validator is in ZEND validation lib
                    if (class_exists(self::ZENDVALIDATORPATH . $validatorName)) {
                        $classname = self::ZENDVALIDATORPATH . $validatorName;
                    } else { // validator not found
                        $this-> valid=FALSE;
                        $logArr[$fieldName][$validatorName] = self::NOTFOUND;
                        continue;
                    }
                }
                $validatorChain = new  ValidatorChain();
                if($validatorParams!=''){
                    try {
                      //  $validatorParams =explode($validatorParams);
                        if(is_array($validatorParams)){

                        }
                        else {
                            eval("\$validatorParams = [$validatorParams];");
                        }

                        $validatorChain->attach(new $classname($validatorParams,$fields));
                    }
                    catch (Exception $e) {
                        $this-> valid=FALSE;
                        $logArr[$fieldName][$validatorName]= $e->getMessage();
                        continue;
                    }
                }
                else {
                    $validatorChain->attach(new $classname());
                }
                // Validate the username
                if ($validatorChain->isValid($fieldValue)) {  //  passed validation
                    $logArr[$fieldName][$validatorName]=self::GOOD;
                }
                else {  // failed validation; print reasons
                    $this-> valid=FALSE;
                    foreach ($validatorChain->getMessages() as $message) {
                        $logArr[$fieldName][$validatorName]="$message";
                    }
                }
            } //end loop 2
        }  //end loop 1

        // for debug: var_dump($this-> valid);var_dump($logArr);die;
        //contain field name, validator name and the result of validation
        return $logArr;
    }


    // delete all keys that have passed validation
    public function errorReduce($logArr)
    {
        foreach ($logArr as $fieldName => $validatorArr) {
            foreach ($validatorArr as $validatorName => $message) {
             if (!strcmp($message,self::GOOD)){
                 unset($logArr[$fieldName][$validatorName]);
             }
            }
            if (empty($logArr[$fieldName])){
                unset($logArr[$fieldName]);
            }
        }
        return $logArr;
    }
}
?>
