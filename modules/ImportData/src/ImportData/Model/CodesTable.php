<?php

namespace ImportData\Model;

use Zend\Db\TableGateway\TableGateway;


class CodesTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function fetchAll()
    {
        $rsArray=array();
        $rs= $this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[]=get_object_vars($r);
        }

        return $rsArray;
    }

    public function save($row){


        $rs= $this->tableGateway->select(array('code' => $row->code,'code_type' => $row->code_type));
        $exist = $rs->current();

        $arrayData = get_object_vars($row);


        if(!empty($exist)){
            $this->tableGateway->update($arrayData, array('code' => $row->code,'code_type' => $row->code_type));
        } else {
            $this->tableGateway->insert($arrayData);
        }

    }

    public function truncate($icdType){

        $this->tableGateway->delete(array('code_type' => $icdType));
    }

}