<?php

namespace ImportData\Lists;

use ImportData\Lists\InterfaceList;
use ImportData\Lists\BaseList;
use ImportData\Controller\ImportDataController;
use ImportData\Model;


class MohDrugs extends BaseList implements InterfaceList
{

    public static $table = ImportDataController::CODE_TABLE;
    public static $columns = array(
                                    'catalog',
                                    'mapping_code',
                                    'drug_name',
                                    'repeats_for_mapping'
                                    );

    public $EDMdata = null;
    public $clinikalData = null;
    public $constantTitle = null;
    public $uniqueListName = 'moh_drugs';


    public function __construct($EDMdata){


        $this->EDMdata = is_object($EDMdata) ? get_object_vars($EDMdata): $EDMdata;
        $this->constantTitle = $this->EDMdata['catalog']."_".$this->EDMdata['mapping_code'];

        if(is_null($this->EDMdata['drug_name']) || $this->EDMdata['drug_name'] === "" || is_null($this->EDMdata['mapping_code'])){
            throw new \Exception('missing/worng data. records was received ' . json_encode($EDMdata));
        }

    }


    public function convertKeys(){

        $this->clinikalData['code_text'] = $this->EDMdata['drug_name'];
        $this->clinikalData['code'] = $this->EDMdata['mapping_code'];
        $this->clinikalData['code_type'] = ImportDataController::MOH_DRUGS;
        $this->clinikalData['active'] =  1 ;
        $this->clinikalData['code_text_short'] =  $this->constantTitle ;




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

