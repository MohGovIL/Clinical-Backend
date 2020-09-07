<?php

namespace GenericTools\Model;

use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Adapter\Adapter;

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
    public function getListWithTheType($type,$pid,$outcome)
    {
        /*

         SELECT l.diagnosis,c.code,c.code_text,ct.ct_id,ct.ct_key
						FROM lists l LEFT JOIN codes c ON (l.diagnosis = concat(SUBSTRING_INDEX(l.diagnosis,':',1),c.code))
					   				 RIGHT JOIN code_types ct ON (ct.ct_id=c.code_type AND  ct.ct_key ="Sensitivities")
         WHERE  c.id IS NOT  NULL

        */
        $this->clearAllJoin();
        $rsArray = array();
        $this->join = $this->appendJoin(
            ["c"=>"codes"],
            new Expression("lists.diagnosis = concat(SUBSTRING_INDEX(lists.diagnosis,':',1),':',c.code)"),
            ['code','code_text'],
            Select::JOIN_LEFT
        );
        $this->join =  $this->appendJoin(
            ["ct"=>"code_types"],
            new Expression("ct.ct_id=c.code_type AND  ct.ct_key = SUBSTRING_INDEX(lists.diagnosis,':',1)"),
            ["ct_id","ct_key"],
            Select::JOIN_RIGHT
        );

        $this->join = $this->getJoins();

        $rs = $this->buildGenericSelect(['type'=>$type,'outcome'=>$outcome,"pid"=>$pid]);

        foreach ($rs as $r) {
            $rsArray[] = xl($r['code_text']);
        }

        return $rsArray;
    }
}
