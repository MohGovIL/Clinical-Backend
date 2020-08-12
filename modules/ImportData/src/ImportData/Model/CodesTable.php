<?php

namespace ImportData\Model;

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGateway;


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

    public function getCodeTitle($code,$code_type){

        $select = $this->tableGateway->getSql()->select();
        $joinExp= new Expression("codes.code_type = ct.ct_id");
        $select->join(array ("ct"=>"code_types") , $joinExp, array("ct_id"=>"ct_id","ct_key"=>"ct_key"), "LEFT");
        $where = new Where();
        $where->nest()->equalTo('code', $code)->AND->equalTo("ct.ct_key",$code_type);

        $select->where($where);
        $rs = $this->tableGateway->selectWith($select);
        $exist = $rs->current();

        if(!empty($exist)){
            return  $exist->code_text;
        } else {
            return null;
        }

    }





}
