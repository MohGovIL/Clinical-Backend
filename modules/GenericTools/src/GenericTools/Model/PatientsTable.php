<?php

namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;

class PatientsTable
{

    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    /**
     * @param null $sortBy
     * lname Sort by last name in case more variables are needed add their corresponding functions
     * @return array
     */


    public function fetchAll($sortBy = null)
    {
        $rsArray=array();
        $rs =$this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[]=get_object_vars($r);
        }
        if($sortBy == 'lname') {
            usort($rsArray, function ($a, $b) {
                return $a['lname'] <=> $b['lname'];
            });
        }
        return $rsArray;
    }

    public function getPatientDataByName($lname, $fname)
    {
        $sql="SELECT * FROM " . $this->tableGateway->table . " WHERE fname = ? AND lname = ?";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($fname, $lname));
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results[0];
    }


    public function getPatientDataById($pid)
    {
        $sql="SELECT * FROM " . $this->tableGateway->table . " WHERE pid = ?";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($pid));
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results[0];
    }
    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getPatientData($pid)
    {
        $rowset = $this->tableGateway->select(array('pid' => $pid));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row;
    }

    /**
     * @param $field
     * @param $pid
     * @param $doctor_id
     * @return bool
     * @throws \Exception
     */
    public function setOneField($field, $pid, $doctor_id)
    {
        $row = $this->getPatientData($pid);
        if ( $row && $doctor_id > 0 && strlen($field) > 0 ) {
            $sql = "UPDATE `patient_data` SET `".$field."` = '".$doctor_id."' WHERE `patient_data`.`pid` = ".$pid;
            sqlStatement($sql);
            return true;
        }
        return false;
    }

    public function setPatientDataById($pid,$raw)
    {

        /*
        $rows=explode(",",str_replace('\'', '', $raw));
        $setSql=array();
        foreach ($rows as $index =>$value){
            $data=explode("=", $value);
            $setSql[trim($data[0])]=$data[1];
        }
        */

        $return=$this->tableGateway->update($raw, array('pid' => $pid));

        /*
        $sql="UPDATE " . $this->tableGateway->table . " SET $raw WHERE pid = ?";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($pid));
        $return = $statement->execute();
        */


        return $return;
    }


    public function searchPatientByName(array $name)
    {
        $sql="SELECT * ,concat(fname,mname,lname) as fullname FROM " . $this->tableGateway->table;

        $bindParams=array();
        if(!empty($name)){
            $sql.=" HAVING fullname like ? ";

                $bindParams[]='%'.$name[0].'%';


            unset($name[0]);
        }

        $index=1;
        while(!empty($name)){
            $sql.=" AND fullname like ? ";

                $bindParams[]='%'.$name[$index].'%';

            unset($name[$index]);
            $index++;
        }

        $statement = $this->tableGateway->adapter->createStatement($sql, $bindParams);
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results;
    }
}
