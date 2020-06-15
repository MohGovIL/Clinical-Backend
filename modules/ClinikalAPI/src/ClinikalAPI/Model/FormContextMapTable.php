<?php

/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 */


namespace ClinikalAPI\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class FormContextMapTable
{
    protected $tableGateway;
    private   $registryArr = array(  "name"=> "name",
        "state" => "state" ,
        "unpackaged" => "unpackaged",
        "sql_run"  => "sql_run",
        "priority"  => "priority",
        "category"  => "category" ,
        "nickname"  => "nickname",
        "aco_spec" => "aco_spec",
        "directory"=>"directory"
    );
    const LEFT_JOIN = "LEFT";


    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getActiveForms($service_type= null,$reason_code = null){

        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();
        $joinExp= new Expression(" form_context_map.form_id = r.id");
        $select->join(array ("r"=>"registry") , $joinExp, $this->registryArr, self::LEFT_JOIN);
        $where = new Where();

        if(!is_null($service_type)){
            $where->nest()->equalTo('context_type', "service_type")->AND->equalTo("context_id",$service_type)->AND->equalTo("r.state","1")->unnest();
        }

        if(!is_null($service_type) && !is_null($reason_code)){
            $where->OR;
        }

        if(!is_null($reason_code)){
            $where->nest()->equalTo('context_type', "reason_code")->AND->equalTo("context_id",$reason_code)->AND->equalTo("r.state","1")->unnest();
        }

        $select->where($where);

        $select->order(array('r.priority ASC'));

        $rs = $this->tableGateway->selectWith($select);
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }

        return $rsArray;
    }
}
