<?php

/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 */


namespace ClinikalAPI\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class ListOptionsTable
{
    protected $tableGateway;
    private   $registryArr = array(  "list_id"=> "list_id",
        "option_id" => "option_id" ,
        "title" => "title",
        "seq"  => "seq",
        "mapping"  => "mapping",
        "notes"  => "notes" ,
        "activity"  => "activity",
    );
   


    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getLionicIndicators($service_type= null,$reason_code = null){

        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();
        
        $where = new Where();

        if(!is_null($service_type)){
            $where->equalTo('list_id', "loinc_org");
        }

       
        $select->where($where);

        $select->order(array('seq ASC'));

        $rs = $this->tableGateway->selectWith($select);
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }

        return $rsArray;
    }
}
