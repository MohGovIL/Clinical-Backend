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


class Icd9List extends BaseList implements InterfaceList
{
    const DIAGNOSE_ID = 3;

    public static $table = ImportDataController::CODE_TABLE;
    public static $columns = array('Code',
                                    'Code2',
                                    'Diag_or_proc',
                                    'Description',
                                    'Description_long',
                                    'Gender_code',
                                    'Min_age',
                                    'Max_age',
                                    'From_date',
                                    'To_date',
                                    'Create_date',
                                    'Update_date');
    public $EDMdata = null;
    public $clinikalData = null;
    public $constantTitle = null;
    public $uniqueListName = 'icd9';


    public function __construct($EDMdata){

        $this->EDMdata = is_object($EDMdata) ? get_object_vars($EDMdata): $EDMdata;
        $this->constantTitle = $this->createConstantTitle($this->EDMdata['Code']."_".$this->EDMdata['Diag_or_proc']);

        if(is_null($this->EDMdata['Code']) || $this->EDMdata['Code'] === "" || is_null($this->EDMdata['Description'])){
            throw new \Exception('missing/worng data. records was received ' . json_encode($EDMdata));
       }
       if(is_null($this->EDMdata['Code2']) || $this->EDMdata['Code2'] === "" || $this->EDMdata['Code2'] === "" ||$this->EDMdata['Diag_or_proc'] != self::DIAGNOSE_ID){
           throw new \Exception('Record not relevant',10);
       }
    }


    public function convertKeys(){

        $this->clinikalData['code_text'] = $this->EDMdata['Description'];
        $this->clinikalData['code'] = $this->EDMdata['Code2'];
        $this->clinikalData['code_type'] = ImportDataController::ICD9_CODE;
        $this->clinikalData['active'] = empty($this->EDMdata['To_date']) ? 1 :0;


    }

    public function getTableObject(){

        $listInstance = new Model\Codes();
        $listInstance->exchangeArray($this->clinikalData);

        return $listInstance;
    }

    public function getTranslation(){

        return array(
        );
    }


}

