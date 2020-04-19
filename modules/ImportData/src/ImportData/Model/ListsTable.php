<?php

namespace ImportData\Model;

use Zend\Db\TableGateway\TableGateway;


class ListsTable
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

    public function save($row, $list_id){

        $rs= $this->tableGateway->select(array('list_id' => $list_id, 'option_id' => $row->option_id));
        $exist = $rs->current();

        $arrayData = get_object_vars($row);
        if(!empty($exist)){
            unset($arrayData['option_id']);
            unset($arrayData['list_id']);
            $this->tableGateway->update($arrayData, array('list_id' => $list_id, 'option_id' => $row->option_id));
        } else {
            $arrayData['list_id'] = $list_id;
            $this->tableGateway->insert($arrayData);
        }

    }

    public function truncateList($listId){

        $this->tableGateway->delete(array('list_id' => $listId));
    }

    public function getCityName($cityId){

        $rs = $this->tableGateway->select(array('list_id' => 'mh_cities', 'option_id' => 'city_' . $cityId));
        $row = $rs->current();
        return is_object($row) ? $row->title : false;
    }

    public function getList($list_name){

        $rsArray=array();
        $rs = $this->tableGateway->select(array('list_id' => $list_name));

        foreach($rs as $r) {
            $rsArray[]=get_object_vars($r);
        }
        return $rsArray;
    }

}