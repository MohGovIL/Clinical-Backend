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


class Icd10List extends BaseList implements InterfaceList
{

    public static $table = ImportDataController::CODE_TABLE;
    public static $columns = array('Code',
                                    'Code2',
                                    'Description',
                                    'Description_long',
                                    'From_date',
                                    'To_date',
                                    'Create_date',
                                    'Update_date');
    public $EDMdata = null;
    public $clinikalData = null;
    public $constantTitle = null;
    public $uniqueListName = 'icd10';


    public function __construct($EDMdata){

        $this->EDMdata = is_object($EDMdata) ? get_object_vars($EDMdata): $EDMdata;

        if(is_null($this->EDMdata['Code']) || $this->EDMdata['Code'] === ""  || is_null($this->EDMdata['Description'])){
            throw new \Exception('missing/worng data. records was received ' . json_encode($EDMdata));
        }
    }


    public function convertKeys(){

        $this->clinikalData['code_text'] = $this->EDMdata['Description'];
        $this->clinikalData['code'] = $this->EDMdata['Code2'];
        $this->clinikalData['code_type'] = ImportDataController::ICD10_CODE;
        $this->clinikalData['active'] = empty($this->EDMdata['To_date']) ? 1 :0;
    }

    public function getTableObject(){

        $listInstance = new Model\Codes();
        $listInstance->exchangeArray($this->clinikalData);

        return $listInstance;
    }

    public function getTranslation(){

        return array();
    }


}

