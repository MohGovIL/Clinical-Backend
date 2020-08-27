<?php


namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;

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

    public function fatchAllByEID($eid,$asString = false){

        $sql="SELECT * FROM " . $this->tableGateway->table . " WHERE eid = ?";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($eid));
        $return = $statement->execute();

        $results = array();


        foreach ($return as $row) {
            $results[] = $row;
        }
        if(!$asString) {
            return $results[0];
        }
        else{
            $arrTemp=[];

            foreach ($results as $code) {
                array_push($arrTemp,$code['reason_code']);
            }
            $str = implode(",",$arrTemp);
            $results = [$str];
        }

        return $results[0];
    }




}
