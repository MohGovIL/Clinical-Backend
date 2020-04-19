<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/23/17
 * Time: 11:17 AM
 */

namespace ImportData\Lists;

use ImportData\Lists\InterfaceList;
use ImportData\Lists\BaseList;
use ImportData\Controller\ImportDataController;
use ImportData\Model;


class FamillystatusList extends BaseList implements InterfaceList
{

    public static $table = ImportDataController::LIST_TABLES;
    public static $columns = array('Code',
                                    'Description',
                                    'Create_date',
                                    'Update_date',
                                    'Code_btl');
    public $EDMdata = null;
    public $clinikalData = null;
    public $constantTitle = null;
    public $uniqueListName = 'famillystatus';


    public function __construct($EDMdata){

        $this->EDMdata = is_object($EDMdata) ? get_object_vars($EDMdata): $EDMdata;
        $this->constantTitle = $this->createConstantTitle($this->EDMdata['Code']);

        if(is_null($this->EDMdata['Code']) || $this->EDMdata['Code'] === ""  || is_null($this->EDMdata['Description'])){
            throw new \Exception('missing/worng data. records was received ' . json_encode($EDMdata));
        }
    }


    public function convertKeys(){


        switch ($this->EDMdata['Code']){
            case '20':
                $this->clinikalData['option_id'] = 'married';
                if(is_null($this->EDMdata['Description_eng']))$this->EDMdata['Description_eng'] = 'Married';
                $this->clinikalData['notes'] = 'M';
                break;
            case '10':
                $this->clinikalData['option_id'] = 'single';
                if(is_null($this->EDMdata['Description_eng']))$this->EDMdata['Description_eng'] = 'Single';
                $this->clinikalData['notes'] = 'S';
                break;
            case '30':
                $this->clinikalData['option_id'] = 'divorced';
                if(is_null($this->EDMdata['Description_eng']))$this->EDMdata['Description_eng'] = 'Divorced';
                $this->clinikalData['notes'] = 'D';
                break;
            case '40':
                $this->clinikalData['option_id'] = 'widowed';
                if(is_null($this->EDMdata['Description_eng']))$this->EDMdata['Description_eng'] = 'Widowed';
                $this->clinikalData['notes'] = 'w';
                break;
            case '21':
                $this->clinikalData['option_id'] = 'separated';
                if(is_null($this->EDMdata['Description_eng']))$this->EDMdata['Description_eng'] = 'Separated';
                $this->clinikalData['notes'] = 'L';
                break;
            case '11':
                $this->clinikalData['option_id'] = 'domestic partner';
                if(is_null($this->EDMdata['Description_eng']))$this->EDMdata['Description_eng'] = 'Domestic Partner';
                $this->clinikalData['notes'] = 'T';
                break;
            default:
                $this->clinikalData['option_id'] = $this->constantTitle;
                $this->clinikalData['notes'] = 'O';
                $this->isNew = true;
                break;

        }

        $this->clinikalData['title'] =$this->constantTitle;
        //$this->clinikalData['list_id'] = '';
        $this->clinikalData['seq'] = '0';
        $this->clinikalData['is_default'] = '0';
        $this->clinikalData['option_value'] = '0';
        $this->clinikalData['mapping'] = '';
        $this->clinikalData['codes'] = '';
        $this->clinikalData['toggle_setting_1'] = '0';
        $this->clinikalData['toggle_setting_2'] = '0';
        $this->clinikalData['activity'] =  empty($this->EDMdata['To_date']) ? 1 :0;
        $this->clinikalData['subtype'] = '';

    }

    public function getTableObject(){

        $listInstance = new Model\Lists();
        $listInstance->exchangeArray($this->clinikalData);

        return $listInstance;
    }

    public function getTranslation(){

        if($this->isNew){
            return array(
                'constant' => $this->clinikalData['title'],
                'english' => $this->clinikalData['title'],
                'hebrew' => $this->EDMdata['Description']
            );
        } else {
            return array(
                'constant' => $this->constantTitle,
                'english' => !empty($this->EDMdata['Description_eng']) ? $this->EDMdata['Description_eng'] : $this->constantTitle,
                'hebrew' => $this->EDMdata['Description']
            );
        }
    }


}