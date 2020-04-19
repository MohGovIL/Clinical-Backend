<?php

namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;

/**
 * Class PumpsTable
 * @package Pouring\Model
 */
class FacilityTable
{
    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getFacility($id)
    {
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row;
    }

    /**
     * @return array -   all of the facilities from facility table
     */
    public function fetchAll()
    {
        $rsArray=array();
        $rs= $this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[]=(array)$r;
        }
        return $rsArray;
    }

    /**
     * @param string $key_key
     * @param string $key_value
     * @return array|bool
     */
    public function getListWithSomeKeyValue($key_key='', $key_value='')
    {
        if(strlen($key_key) <=0 || strlen($key_value) <=0 ) return false;

        $rsArray=array();
        $rs= $this->tableGateway->select();
        foreach($rs as $r) {
            $r = (array)$r;
            if(strlen($r[$key_key]) <=0 || strlen($r[$key_value]) <=0 ) continue;

            if(array_key_exists($key_key, $r) && array_key_exists($key_value, $r) ) {
                $rsArray[$r[$key_key]] = $r[$key_value];
            }

        }
        return $rsArray;
    }

    /**
     * @param bool $sortAlphaBeta
     * @return array
     */
    public function getListForViewForm($sortAlphaBeta = false)
    {
        $select = new Select('facility');
        if($sortAlphaBeta) {
            $select->order('name ASC');
        }
        $results = $this->tableGateway->selectWith($select);
        $result = array();
        foreach ($results as $row) {
            $result[$row->id] = !$sortAlphaBeta ? xlt($row->name) : xlt($row->name);
        }
        return $result;
    }
}
