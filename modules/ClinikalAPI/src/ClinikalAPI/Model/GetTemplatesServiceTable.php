<?php

/**
 * Date: 02/06/2020
 *  @author Dror Golan <drorgo@matrix.co.il>
 */


namespace ClinikalAPI\Model;

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGateway;

class GetTemplatesServiceTable
{
    const LEFT_JOIN = "LEFT";
    protected $tableGateway;
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    private   $listArr = array(
        "option_id" => "option_id" ,
        "title" => "title" ,
        "seq"  => "seq",
    );
    public function getTemplatesForForm($form_id=null,$form_field=null,$service_type=null,$reason_code=null){

        if($form_id === null  || $form_field === null || $service_type ===null || $reason_code===null) //primary keys cannot be null
            return null;

        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();
        $joinExp= new Expression("clinikal_templates_map.message_id = list.option_id AND list.list_id = 'clinikal_templates' ");
        $select->join(array ("list"=>"list_options") , $joinExp, $this->listArr, self::LEFT_JOIN);
        $where = new Where();


        if(!is_null($service_type)){
            $where->equalTo("service_type",$service_type)->AND->
                    in("reason_code",explode(",",$reason_code))->AND->
                    equalTo("form_field",$form_field)->AND->
                    equalTo("form_id",$form_id);
        }

        $select->where($where);
        $select->order(array('clinikal_templates_map.seq ASC'));
        //$debug = $select->getSqlString();
        $rs = $this->tableGateway->selectWith($select);
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }

        return $rsArray;
    }
}
