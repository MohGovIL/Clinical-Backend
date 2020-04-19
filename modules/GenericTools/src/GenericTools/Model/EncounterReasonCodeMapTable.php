<?php


namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;

class EncounterReasonCodeMapTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function insertValueSets($data){


        if (empty($data)){
            return false;
        }
        $fieldsArray=array();
        $sql= "INSERT INTO encounter_reasoncode_map (eid,reason_code) VALUES ";

        foreach ($data as $index => $record){
            if (!is_null($record['eid']) && !is_null($record['reason_code'])){
                $sql .="(?,?),";
                $fieldsArray[]=$record['eid'];
                $fieldsArray[]=$record['reason_code'];

            }else{
                return false;
            }
        }
        $sql=substr_replace($sql,"",strlen($sql)-1,1);
        $statement = $this->tableGateway->adapter->createStatement($sql, $fieldsArray);
        $resultObj = $statement->execute();
        return $resultObj->getAffectedRows();

    }

    public function deleteValueSetsById($eid){


        if (empty($eid)){
            return false;
        }
        $fieldsArray=array();
        $sql= "DELETE FROM encounter_reasoncode_map WHERE eid = ? ";
        $fieldsArray[]=intval($eid);

        $statement = $this->tableGateway->adapter->createStatement($sql, $fieldsArray);
        $resultObj = $statement->execute();
        return $resultObj->getAffectedRows();

    }




}
