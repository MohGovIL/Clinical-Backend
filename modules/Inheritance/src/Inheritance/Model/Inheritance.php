<?php
/**
 * Created by PhpStorm.
 * User: shaharzi
 * Date: 04/09/16
 * Time: 21:13
 */


namespace Inheritance\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/*
 * this is the basic structure of Model
 * */
class Inheritance /*implements InputFilterAwareInterface*/
{
    //properties of all columns (from sql table) that we want

    public $id;
    public $from_date;
    public $command;
    public $drug;
    public $daily_dosage;
    public $units;
    public $part_1;
    public $part_2;
    public $user_name;
    public $notes;

//    //validation!
//    //here we add all the validation - have a lot of another filters and validators in zend.
//    //we use with this validation also to client side validation
//    public static $inputsValidations = array(
//        //some exist example
//        array(
//            'name'     => 'id',
//            'filters'  => array(
//                array('name' => 'Int'),
//            ),
//        ),
//        array(
//            'name' => 'company_name',
//            'required' => true,
//            'filters' => array(
//                array('name' => 'StripTags'),
//                array('name' => 'StringTrim')
//            )
//        ),
//
//        array(
//            'name'     => 'speed',
//            'required' => true,
//            'filters'  => array(
//                array('name' => 'Int'),
//            ),
//        ),
//        array(
//            'name'     => 'pipe_diameter',
//            'required' => true,
//            'filters'  => array(
//                array('name' => 'Int'),
//            ),
//        ),
//    );
//
//    protected $inputFilter;

    //must add it for all the properties
    public function exchangeArray($data)
    {
        $this->id  = (!empty($data['id'])) ? $data['id'] : null;
        $this->from_date     = (!empty($data['from_date'])) ? $data['from_date'] : null;
        $this->command = (!empty($data['command'])) ? $data['command'] : null;
        $this->drug = (!empty($data['drug'])) ? $data['drug'] : null;
        $this->daily_dosage = (!empty($data['daily_dosage'])) ? $data['daily_dosage'] : null;
        $this->units = (!empty($data['units'])) ? $data['units'] : null;
        $this->part_1 = (!empty($data['part_1'])) ? $data['part_1'] : null;
        $this->part_2 = (!empty($data['part_2'])) ? $data['part_2'] : null;
        $this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;
        $this->notes = (!empty($data['notes'])) ? $data['notes'] : null;
    }

//    public function setInputFilter(InputFilterInterface $inputFilter)
//    {
//        throw new \Exception("Not used");
//    }
//
//    public function getInputFilter()
//    {
//        if (!$this->inputFilter) {
//            $inputFilter = new InputFilter();
//            //adding the filters
//            foreach(self::$inputsValidations as $input) {
//                $inputFilter->add($input);
//            }
//
//            $this->inputFilter = $inputFilter;
//        }
//
//        return $this->inputFilter;
//    }

}