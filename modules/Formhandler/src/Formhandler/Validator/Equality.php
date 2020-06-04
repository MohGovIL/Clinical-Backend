<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 9/4/16
 * Time: 4:38 PM
 */

namespace Formhandler\Validator;
use DateTime;
use SqlParser\Context;
use Laminas\Validator\AbstractValidator;

class Equality extends AbstractValidator
{
    const PROBLEMWITHDATE = 'PROBLEMWITHDATE';
    const PROBLEMWITHDEFAULT = 'PROBLEMWITHDEFAULT';
    const ISADATE = 'ISADATE';
    const NOVALUE = 'NOVALUE';

    public  $messageTemplates = array(
        self::PROBLEMWITHDATE => 'Date must be equal or less then other date',
        self::PROBLEMWITHDEFAULT => 'Value must be greater or equal to the other values',
        self::ISADATE => 'Date must be in a date format',
        self::NOVALUE => 'No value was set to field'
    );


    public $secondElement;
    public $sign;
    public $fields;
    public $type;

    public function __construct(array $options = array(),array $fields = array())
    {
        parent::__construct($options);

        $this->secondElement= $options[1];
        $this->sign= $options[0];
        $this->fields=$fields;
        $this->type=$options[2];

    }
    public function compare($a,$b,$sign)
    {
        switch ($this->sign)
        {
            case ">":
                return ($a>$b?true:false);
                break;
            case ">=":
                return ($a>=$b?true:false);
                break;
            case "<":
                return ($a<$b?true:false);
                break;
            case "<=":
                return ($a<=$b?true:false);
                break;
            case "!=":
                return ($a!=$b?true:false);
                break;
            case "==":
                return ($a==$b?true:false);
                break;

        }
        return null;


    }
    public function isValid($value)
    {


        switch ($this->type){
            case "Date":
            case "DateTime":
            //$compareWith= new DateTime($this->fields[$this->secondElement]);
            // $compareThis= new DateTime($value);
            $compareWith=$this->fields[$this->secondElement];
            $compareThis=$value;
            if(!$this->compare($compareThis,$compareWith,$this->sign))
                 {

//    $this->messageTemplates[self::PROBLEMWITHDATE] .=str_replace("_"," ",$this->secondElement )." date.";
                     $this->error(self::PROBLEMWITHDATE);
                     return false;
                 }
                 return true;
            break;

            case "Number":
            default:
                $compareWith= $this->fields[$this->secondElement];
                $compareThis= $value;

                if(!$this->compare($compareThis,$compareWith,$this->sign))
                {

//    $this->messageTemplates[self::PROBLEMWITHDATE] .=str_replace("_"," ",$this->secondElement )." date.";
                    $this->error(self::PROBLEMWITHDEFAULT);
                    return false;
                }
                return true;


            break;
        }

        $this->error(self::NOVALUE);
        return false;


    }
}


?>
