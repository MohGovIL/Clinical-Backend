<?php



namespace Inheritance\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/*
 * this is the basic structure of Model
 * */
class Lists
{
    //properties of all columns (from sql table) that we want

    public $list_id;
    public $option_id;
    public $title;
    public $is_default;
    public $option_value;
    public $mapping;
    public $codes;
    public $toggle_setting_1;
    public $toggle_setting_2;
    public $activity;
    public $subtype;



    //must add it for all the properties
    public function exchangeArray($data)
    {

        $this->list_id  = (!empty($data['list_id'])) ? $data['list_id'] : null;
        $this->option_id = (!empty($data['option_id'])) ? $data['option_id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->is_default = (!empty($data['is_default'])) ? $data['is_default'] : null;
        $this->option_value = (!empty($data['option_value;'])) ? $data['option_value;'] : null;
        $this->mapping = (!empty($data['mapping'])) ? $data['mapping'] : null;
        $this->codes = (!empty($data['codes'])) ? $data['codes'] : null;
        $this->toggle_setting_1 = (!empty($data['toggle_setting_1'])) ? $data['toggle_setting_1'] : null;
        $this->toggle_setting_2 = (!empty($data['toggle_setting_2'])) ? $data['toggle_setting_2'] : null;
        $this->activity = (!empty($data['activity'])) ? $data['activity'] : null;
        $this->subtype = (!empty($data['subtype'])) ? $data['subtype'] : null;
    }


}