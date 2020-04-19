<?php

namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;

/**
 * Class PumpsTable
 * @package Pouring\Model
 */
class UserTable
{
    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null|false
     */
    public function getUser($id)
    {
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    /**
     * @return array -   all of the active users from user table
     */
    public function fetchAll()
    {
        $rsArray=array();
        $rs= $this->tableGateway->select(array('authorized' =>1, 'active' => 1));
        foreach($rs as $r) {
            $rsArray[]=(array)$r;
        }
        return $rsArray;
    }

    /**
     * @return array -   all of the active users from user table
     */
    public function fetchAllLastNameThenFirst()
    {
        $rsArray=array();

        $select = $this->tableGateway->getSql()->select();
        $where = new Where();
        $where->equalTo('authorized', 1);
        $where->AND->equalTo('active', 1);

        $select->order(array('lname ASC','fname ASC'));
        $select->where($where);
        $rs = $this->tableGateway->selectWith($select);

        foreach($rs as $r) {
            $rsArray[]=(array)$r;
        }
        return $rsArray;
    }








    /**
     * @return array -   all of the active users from user table
     */
    public function fetchList()
    {
        $rsArray=array();
        $rs= $this->tableGateway->select(array('authorized' =>1, 'active' => 1));
        foreach($rs as $r) {
            $rsArray[$r->id]=(array)$r;
        }
        return $rsArray;
    }

    /**
     * @return array -   all of the active users from user table
     */
    public function fetchListForViewForm()
    {
        $rsArray=array();
        $rs= $this->tableGateway->select(array('authorized' =>1, 'active' => 1));
        foreach($rs as $r) {
            $rsArray[$r->id]=$r->fname . ' ' . $r->lname;
        }
        return $rsArray;
    }

    /**
     * @return array -   all of the users from user table
     */
    public function fetchAllNoFilters()
    {
        $rsArray=array();
        $rs= $this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[$r->id]=(array)$r;
        }
        return $rsArray;
    }

    public function getUserName($userId)
    {
        $rowset = $this->tableGateway->select(array('id' => $userId));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row->fname . ' ' . $row->lname;
    }

    /**
     * @param $FilterData
     * @return array|bool
     */
    public function fetchListForViewFormByField($FilterData)
    {
        if( count($FilterData) <= 0 )return false;
        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();

        $where = new Where();
        foreach($FilterData as $field=>$value){
            $where->AND->equalTo($field, $value);
        }
        $select->where($where);
        $rs = $this->tableGateway->selectWith($select);
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }
        return $rsArray;
    }


    /** search user by name and active param
     * the function checks if a every value in the name array
     * is present in the full name of the user
     * @param array
     * @param bool
     * @return array
     */
    public function searchUserByName(array $name,$active=null)
    {

        $results = array();
        if(empty($name)){
            return $results;
        }

        $sql="SELECT * ,concat(fname,mname,lname) as fullname FROM " . $this->tableGateway->table;

        $bindParams=array();
        if(!empty($name)){
            $sql.=" HAVING fullname like ? ";
            $bindParams[]='%'.$name[0].'%';
            unset($name[0]);
        }

        $index=1;
        while(!empty($name)){
            $sql.=" AND fullname like ? ";
            $bindParams[]='%'.$name[$index].'%';
            unset($name[$index]);
            $index++;
        }

        if(!is_null($active)){
            $sql.=" AND active = ? ";
            $bindParams[]=intval($active);
        }


        $statement = $this->tableGateway->adapter->createStatement($sql, $bindParams);
        $return = $statement->execute();


        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results;
    }


}
