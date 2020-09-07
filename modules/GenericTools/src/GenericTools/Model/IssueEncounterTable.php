<?php

namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\Sql\Expression;

class IssueEncounterTable
{
    use baseTable;

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
