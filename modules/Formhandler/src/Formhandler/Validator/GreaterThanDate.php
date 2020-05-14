<?php

namespace Formhandler\Validator;
use Laminas\Validator\AbstractValidator;


class GreaterThanDate extends AbstractValidator
{
    const PROBLEMWITHDATE = 'PROBLEMWITHDATE';
    public  $messageTemplates = array(
        self::PROBLEMWITHDATE => 'Date must be equal or less then other date'

    );

    /**
     * GreaterThanDate constructor.
     * @param array $options - 3 params for equality -
     * 1) the validator value of the field
     * 2) the name of the field
     * 3) check for equal (true,false)
     *
     * @param array $fields - values of all of the form fields.
     */
    public function __construct(array $options = array(),array $fields = array())
    {
        parent::__construct($options);

        $this->valueFromClient =$fields[$options[1]];
        $this->validatorLimitToCheck= $options[0];
        $this->checkIfEqual = $options[2];
    }
    public function isValid($value)
    {
        if($value == null || $value=="")
            return true;

        $validatorLimitToCheck   =   date("Y-m-d",strtotime(str_replace('/', '-',$this->validatorLimitToCheck)));
        $valueFromClient         =   date("Y-m-d",strtotime(str_replace('/', '-',$this->valueFromClient)));
        $checkIfEqual           =   $this->checkIfEqual;
        if($checkIfEqual){
          if($validatorLimitToCheck<=$valueFromClient) {
              return true;
          }
        }
        else{
            if($validatorLimitToCheck<$valueFromClient) {
                return true;
            }
        }

        $this->error(self::PROBLEMWITHDATE);
        return false;


    }
}
