<?php


namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;

class EventCodeReasonMapTable
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
        $sql= "INSERT INTO event_codeReason_map (event_id,option_id) VALUES ";

        foreach ($data as $index => $record){
            if (!is_null($record['event_id']) && !is_null($record['option_id'])){
                $sql .="(?,?),";
                $fieldsArray[]=$record['event_id'];
                $fieldsArray[]=$record['option_id'];

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
        $sql= "DELETE FROM event_codeReason_map WHERE event_id = ? ";
        $fieldsArray[]=intval($eid);

        $statement = $this->tableGateway->adapter->createStatement($sql, $fieldsArray);
        $resultObj = $statement->execute();
        return $resultObj->getAffectedRows();

    }




}
