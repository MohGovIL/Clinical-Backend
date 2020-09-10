<?php

namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Adapter\Adapter;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\Sql\Expression;

class ListsOpenEmrTable
{
    use baseTable;
    use JoinBuilder;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->join = $this->joinTables();
    }

    private function joinTables()
    {
        $this->appendJoin(
            ["ie"=>"issue_encounter"],
            new Expression("ie.pid=lists.pid AND ie.list_id=id"),
            ['encounter'=>'encounter','resolved'=>'resolved'],
            Select::JOIN_LEFT
        );

        return $this->getJoins();
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
