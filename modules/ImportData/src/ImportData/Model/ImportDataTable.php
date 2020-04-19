<?php

namespace ImportData\Model;

use Zend\Db\TableGateway\TableGateway;

class ImportDataTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getAll()
    {
        $rs= $this->tableGateway->select();
        $rsArray = array();
        foreach($rs as $r) {
            $rsArray[]=$r;
        }
        return $rsArray;
    }

    public function fetch(array $params){

        $result= $this->tableGateway->select($params);
        $row = $result->current();
        return $row;
    }

    public function updateDate($id, $update_date){

        $this->tableGateway->update(array('update_at' => $update_date), array('id' => $id));
    }


    public function getEdmLists(){

        $results = $this->tableGateway->select(array('source' => 'EDM'));
        foreach($results as $r) {
            $rsArray[]=$r;
        }
        return $rsArray;
    }

    public function getCsvLists(){

        $results = $this->tableGateway->select(array('source' => 'CSV'));
        foreach($results as $r) {
            $rsArray[]=$r;
        }
        return $rsArray;
    }

}
