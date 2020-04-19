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


class MkcbList extends BaseList implements InterfaceList
{

    public static $table = ImportDataController::LIST_TABLES;
    public static $columns = array('Institute_code',
                                   'Branch_code',
                                   'Heb_branch_name',
                                   'English_branch_name',
                                   'Type_of_facility_code',
                                   'Type_of_facility',
                                   'area',
                                   'city',
                                   'street',
                                   'number');
    public $EDMdata = null;
    public $clinikalData = null;
    public $constantTitle = null;
    public $uniqueListName = 'mkcd';


    public function __construct($EDMdata){

        $this->EDMdata = is_object($EDMdata) ? get_object_vars($EDMdata): $EDMdata;
        $this->constantTitle = $this->createConstantTitle($this->EDMdata['Institute_code'].'-'.$this->EDMdata['Branch_code']);

        if(is_null($this->EDMdata['Branch_code']) || $this->EDMdata['Branch_code'] === ""  || !(is_numeric( $this->EDMdata['Branch_code'])) ){
            throw new \Exception('missing/worng data. records was received ' . json_encode($EDMdata));
        }
        if(is_null($this->EDMdata['Institute_code']) || $this->EDMdata['Institute_code'] === ""  || !(is_numeric( $this->EDMdata['Institute_code']) ) ){
            throw new \Exception('missing/worng data. records was received ' . json_encode($EDMdata));
        }
        if(is_null($this->EDMdata['Heb_branch_name']) || $this->EDMdata['Heb_branch_name'] === ""  ){
            throw new \Exception('missing/worng data. records was received ' . json_encode($EDMdata));
        }
    }


    public function convertKeys(){

        //$this->clinikalData['list_id'] = '';
        $this->clinikalData['option_id'] =$this->constantTitle;
        $this->clinikalData['title'] =$this->constantTitle;;
        $this->clinikalData['seq'] = '0';
        $this->clinikalData['is_default'] = '0';
        $this->clinikalData['option_value'] = '0';
        $this->clinikalData['mapping'] = '';
        $this->clinikalData['notes'] =  json_encode($this->EDMdata);
        $this->clinikalData['codes'] = '';
        $this->clinikalData['toggle_setting_1'] = '0';
        $this->clinikalData['toggle_setting_2'] = '0';
        $this->clinikalData['activity'] =  1 ;
        $this->clinikalData['subtype'] = '';

    }

    public function getTableObject(){

        $listInstance = new Model\Lists();
        $listInstance->exchangeArray($this->clinikalData);

        return $listInstance;
    }

    public function getTranslation(){

        return array(
            'constant' => $this->constantTitle,
            'english' => !empty($this->EDMdata['English_branch_name']) ? $this->EDMdata['English_branch_name'] : $this->constantTitle,
            'hebrew' => $this->EDMdata['Heb_branch_name']
        );
    }


}