<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 19/09/16
 * Time: 10:54
 */

namespace Formhandler\Validator;

use Zend\Validator\AbstractValidator;

class RequiredBy extends AbstractValidator
{
    const PROBLEMWITHREQUIRED = 'PROBLEMWITHDATE';

    public  $messageTemplates = array(
        self::PROBLEMWITHREQUIRED => 'This field is required',

    );


    public $ruleValue;
    public $fieldNameToCheck;
    public $fields;
    public function __construct(array $options = array(),array $fields = array())
    {
        parent::__construct($options);
        $this->ruleValue= $options[1];
        $this->fieldNameToCheck= $options[0];
        $this->fields= $fields;
    }

    public function isValid($value)
    {

        if($this->fields[$this->fieldNameToCheck]==$this->ruleValue)
        {
            if($value!='')
                return true;
            else{
               $this->error(self::PROBLEMWITHREQUIRED);
                return false;
            }
        }

        return true;


    }
}


?>