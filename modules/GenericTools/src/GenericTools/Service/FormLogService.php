<?php


namespace GenericTools\Service;

use Interop\Container\ContainerInterface;
use GenericTools\Model\FormLogTables;

class FormLogService
{
    private $dbAdapter;
    private $formLogTables;

    CONST EMPTY_UPDATE='';

    public function __construct(ContainerInterface $container)
    {
        $this->dbAdapter = $container->get('Laminas\Db\Adapter\Adapter');;
    }

    // Function saves changes made in form to the log tables
    public function saveChanges($formId ,array $changes, $reasonType , $reasonText = '' ,$by=null, $datetime=null)
    {
        if(!empty($changes)){

            if($by===null){
                $by=$_SESSION['authUserID'];
            }

            if($datetime===null){
                $datetime=date('Y-m-d H:i:s');
            }

            if($datetime===null){
                $datetime=date('Y-m-d H:i:s');
            }

            $updateId=$this->getFormLogTables()->saveUpdateReason($formId,$reasonType,$reasonText,$datetime,$by);

            if($updateId){
                foreach($changes as $field=> $fieldChanges){
                    $rowId=$this->getFormLogTables()->insertRecord($updateId,$field,$fieldChanges['before'],$fieldChanges['after']);
                }
            }else{
                return false;
            }

        }

        return true;
    }

    // Get certain logs from log tables
    public function getLog($formId, $fromDate, $toDate, $rowId=null)
    {
        $logsResult = $this->getFormLogTables()->getFormLogs($formId, $fromDate, $toDate, $rowId);
        return $logsResult;
    }

    // Get instance of log table object
    protected function getFormLogTables()
    {
        if (!$this->formLogTables) {
            $this->formLogTables = new FormLogTables($this->dbAdapter);
        }
        return $this->formLogTables;
    }

    //return the entries from array1 that are not present in array2
    public function extractChanges(array $before , array $after)
    {

        //$debug=array_diff($after,$before);
        $changes=array();

        $allFields=array_merge(array_keys($before),array_keys($after));

        foreach ($allFields as $index=>$fieldName){
            if($before[$fieldName] === "") {
                $before[$fieldName] = NULL;
            }
            if($after[$fieldName] === "") {
                $after[$fieldName] = NULL;
            }
            if ($before[$fieldName]!==$after[$fieldName]){
                $beforeVal= ($before[$fieldName]===NULL) ? self::EMPTY_UPDATE : $before[$fieldName] ;
                $afterVal=  ($after[$fieldName]===NULL) ? self::EMPTY_UPDATE : $after[$fieldName] ;
                $changes[$fieldName]=array('before'=>$beforeVal,'after'=>$afterVal );
            }
        }

        return $changes;


    }

    //return $fullArray only with fields that in $onlyFields
    public function filterArray(array $fullArray , array $onlyFields)
    {
        foreach ($fullArray as $key=>$value){

            if ( ! array_key_exists($key, $onlyFields) ){
                unset($fullArray[$key]);
            }
        }

        return $fullArray;
    }

    // Given array of field keys will return array of their info
    public function getFieldsInfo($fieldsKeys)
    {
        $fieldsInfo = $this->getFormLogTables()->getFieldsInfo($fieldsKeys);
        return $fieldsInfo;
    }


}
