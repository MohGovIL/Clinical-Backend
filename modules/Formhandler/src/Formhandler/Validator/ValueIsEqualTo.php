<?php
/**
 * Created by PhpStorm.
 * User: drorgo
 * Date: 21/11/2019
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use DateTime;
use SqlParser\Context;
use Zend\Validator\AbstractValidator;

class ValueIsEqualTo extends AbstractValidator
{
    const PROBLEMWITHVALUE = 'PROBLEMWITHVALUE';
    const NOVALUE = 'NOVALUE';

    public  $messageTemplates = array(
        self::PROBLEMWITHVALUE => 'Value must be equal to',
        self::NOVALUE => 'No values were set into the fields in order to make the equality check'
    );


    public $secondElement;
    public $sign;
    public $fields;
    public $type;

    public function __construct(array $options = array(),array $fields = array())
    {
        parent::__construct($options);

        $this->leftSideValue= $fields[$options[1]];
        $this->rightSideValue= $options[2];
        $this->checkForEmptyValue = $options[0];
        $this->fields=$fields;


    }

    public function isValid($value)
    {
        if($this->checkForEmptyValue == "any" && !empty($this->leftSideValue) && !is_null($this->leftSideValue)  && $this->leftSideValue!=""){
            return true;
        }

        if(($this->leftSideValue=="" || $this->rightSideValue=="") && $this->checkForEmptyValue!="empty")
        {
            $this->error(xlt(self::NOVALUE));
            return false;
        }

        if($this->checkForEmptyValue=="empty"){
            if(is_null($this->leftSideValue) || empty($this->leftSideValue) ) {
                return true;
            }
            else{
                return  false;
            }
        }


        if($this->leftSideValue==$this->rightSideValue){
            return true;
        }
        $this->error($this->messageTemplates[self::PROBLEMWITHVALUE]);
        return false;

    }
}


?>
