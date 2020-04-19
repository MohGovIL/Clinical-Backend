<?php

namespace Inheritance\Model;

use Zend\Db\TableGateway\TableGateway;
use Inheritance\Model\ErrorException;

/**
 * Class PumpsTable
 * @package Inheritance\Model
 */
class NetworkingDBTable
{

    /**
     * CalibrationPumpTable constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function isHasConnection(array $params)
    {
        $rowset = $this->tableGateway->select($params);
        $row = $rowset->current();
        return (empty($row)) ? false : true;
    }

    /**
     * @param Pumps $pumps
     * @throws \Exception
     */
    public function save(NetworkingDB $networking)
    {
        $data = get_object_vars($networking);

        $id = (int)$networking->id;
        if ($id == 0) {
            unset($data['id']);
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
            $networking->id = $id;
        } else {
            $update  = $this->tableGateway->update($data, array('id' => $id));
        }

        return (array) $networking;
    }

    public function getSingleByClinicId($clinic_id)
    {
        /* $resultSet = $this->tableGateway->select();
         return $resultSet;*/

        $rs = $this->tableGateway->select(array('clinic_id' => $clinic_id));
        $row = $rs->current();
        return $row;
    }
    public function removeByClinicID($id){
        $rs = $this->tableGateway->delete(array('clinic_id' => $id));
        return $rs;
    }


    public function getPassword($id){

        $rs = $this->tableGateway->select(array('id' => $id));
        $row = $rs->current();
        return $row->password;
    }



}