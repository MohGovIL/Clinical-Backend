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


class StreetList extends BaseList implements InterfaceList
{

    public static $table = ImportDataController::LIST_TABLES;
    public static $columns = array('Street_code',
                                    'Street_post_code',
                                    'City_code',
                                    'Street_desc',
                                    'X_cordinate',
                                    'Y_cordinate',
                                    'From_date',
                                    'To_date',
                                    'Create_date',
                                    'Update_date');
    public $EDMdata = null;
    public $clinikalData = null;
    public $constantTitle = null;
    public $uniqueListName = 'street';


    public function __construct($EDMdata){

        $this->EDMdata = is_object($EDMdata) ? get_object_vars($EDMdata): $EDMdata;
        $this->constantTitle = $this->createConstantTitle($this->EDMdata['Street_code'] . '_' . $this->EDMdata['City_code']);

        if(is_null($this->EDMdata['Street_code']) || $this->EDMdata['Code'] === ""  || is_null($this->EDMdata['Street_desc']) || is_null($this->EDMdata['City_code'])){
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
        $this->clinikalData['notes'] = $this->EDMdata['City_code'];
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

        return array(
            'constant' => $this->constantTitle,
            'english' => !empty($this->EDMdata['Street_desc_eng']) ? $this->EDMdata['Street_desc_eng'] : $this->constantTitle,
            'hebrew' => $this->EDMdata['Street_desc']
        );
    }


}