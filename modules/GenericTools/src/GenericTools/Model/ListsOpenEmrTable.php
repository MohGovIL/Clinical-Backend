<?php

namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;
use Laminas\Db\Adapter\Adapter;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class ListsOpenEmrTable
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

    public function fetchListUpToDate($listType,$endDate,$pid)
    {

        //type,enddate,pid

        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();

        $where = new Where();
        $where->AND->equalTo("pid", $pid);
        $where->AND->equalTo("type", $listType);
        $where->AND->NEST->lessThan("enddate", $endDate)->OR->isNull('enddate')->UNNEST;


        $select->where($where);
        //$select->order(array('vaccination_date'));

        $rs = $this->tableGateway->selectWith($select);
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }
        return $rsArray;
    }

}
