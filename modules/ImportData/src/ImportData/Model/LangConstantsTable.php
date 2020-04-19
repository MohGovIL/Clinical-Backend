<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/26/17
 * Time: 1:52 PM
 */

namespace ImportData\Model;

use Zend\Db\TableGateway\TableGateway;

class LangConstantsTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function getConstantId($constant){
        //binary for case sensitive
        $sql = "SELECT cons_id FROM " . $this->tableGateway->table . " WHERE constant_name = ? COLLATE utf8_bin";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($constant));
        $return = $statement->execute();

        $row = $return->current();
        return (!empty($row)) ? $row['cons_id'] : false;
    }


    public function save($constantId,$constantName){

        if(!$constantId){
            $this->tableGateway->insert(array('constant_name' => $constantName));
            $constantId = $this->tableGateway->getLastInsertValue();
        } else {
            $this->tableGateway->update(array('constant_name' => $constantName), array('cons_id' => $constantId));
        }

        return $constantId;

    }
}