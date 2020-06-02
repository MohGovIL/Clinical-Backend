<?php

namespace Inheritance\Model;

use Laminas\Db\TableGateway\TableGateway;
use Inheritance\Model\ErrorException;

/**
 * Class PumpsTable
 * @package Inheritance\Model
 */
class CodesTable
{
    CONST ICD9="9909";
    CONST ICD10="9910";


    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function fetchAll()
    {
        /* $resultSet = $this->tableGateway->select();
         return $resultSet;*/

        $rsArray=array();
        $rs= $this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[]=get_object_vars($r);
        }

        return $rsArray;
    }

    public function fetchPartialList($offset,$limit,$list_type,$search)
    {
        $query="SELECT *  FROM codes WHERE code_type = '".$list_type."' AND code LIKE '%".$search."%' LIMIT ".(int)$limit." OFFSET ".(int)$offset."";
        $statement = $this->tableGateway->getAdapter()->query($query);
        $res = ErrorException::execute($statement);

        $rsArray=array();
        foreach($res as $r) {
            $rs['name']= xlt($r['code_text']);
            $rs['code']=$r['code'];
            $rs['enable_disable']=$r['inheritable'];
            $rsArray[]=$rs;
        }
        return $rsArray;
    }

    public function countList($list_type,$search)
    {
        $query="SELECT COUNT(*) as count  FROM codes WHERE code_type = '".$list_type."' AND code LIKE '%".$search."%' ";
        $statement = $this->tableGateway->getAdapter()->query($query);
        $res = ErrorException::execute($statement);


        $rsArray=array();
        foreach($res as $r) {
            $rs['count']=$r['count'];
            $rsArray[]=$rs;
        }
        return $rsArray[0]['count'];
    }


    public function updateInheritable($icd_code,$code_id,$state)
    {
        $state_val= ($state=='true') ? "1" : "0";
        $query="UPDATE codes SET inheritable='".$state_val."' WHERE  code_type='".$icd_code."' AND code='".$code_id."'";

        $statement = $this->tableGateway->getAdapter()->query($query);
        $res = ErrorException::execute($statement);

        return ($res) ? true : false ;
    }


    public function updateAllInheritable($state,$list_type)
    {
        $state_val= ($state==true) ? "1" : "0";
        $query="UPDATE codes SET inheritable='".$state_val."' WHERE  code_type='".$list_type."'  ";

        $statement = $this->tableGateway->getAdapter()->query($query);
        $res = ErrorException::execute($statement);

        return ($res) ? true : false ;
    }

}
