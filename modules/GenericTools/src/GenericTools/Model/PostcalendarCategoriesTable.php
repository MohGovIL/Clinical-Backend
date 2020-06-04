<?php

namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Where;

/**
 * Class PumpsTable
 * @package Pouring\Model
 */
class PostcalendarCategoriesTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function fetchAll($params=array(),$asList=false)
    {
        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();
        $select->where($params);
        $rs = $this->tableGateway->selectWith($select);

        if($asList){
            foreach ($rs as $r) {
                $temp=get_object_vars($r);
                $rsArray[$temp['pc_catid']] = xl($temp['pc_catname']);
            }
        }
        else{
            foreach ($rs as $r) {
                $rsArray[] = get_object_vars($r);
            }
        }


        return $rsArray;
    }



}
