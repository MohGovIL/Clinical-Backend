<?php



namespace Inheritance\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/*
 * this is the basic structure of Model
 * */
class Codes
{
    //properties of all columns (from sql table) that we want

    public $id;
    public $code_text;
    public $code_text_short;
    public $code;
    public $code_type;
    public $active;
    public $inheritable;




    //must add it for all the properties
    public function exchangeArray($data)
    {

        $this->id  = (!empty($data['id'])) ? $data['id'] : null;
        $this->code_text = (!empty($data['code_text'])) ? $data['code_text'] : null;
        $this->code_text_short = (!empty($data['code_text_short'])) ? $data['code_text_short'] : null;
        $this->code = (!empty($data['code'])) ? $data['code'] : null;
        $this->code_type= (!empty($data['code_type;'])) ? $data['code_type;'] : null;
        $this->active = (!empty($data['active'])) ? $data['active'] : null;
        $this->inheritable = (!empty($data['inheritable'])) ? $data['inheritable'] : null;

    }


}