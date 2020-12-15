<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/26/17
 * Time: 1:52 PM
 */

namespace ImportData\Model;

use Laminas\Db\TableGateway\TableGateway;

class LangConstantsTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        require ($GLOBALS['OE_SITE_DIR'] . "/sqlconf.php");
        $this->db_encoding = $sqlconf["db_encoding"];
    }


    public function getConstantId($constant){

        if (!empty($this->db_encoding) && ( $this->db_encoding == "utf8mb4")) {
            $case_sensitive_collation = "COLLATE utf8mb4_bin";
        } else {
            $case_sensitive_collation = "COLLATE utf8_bin";
        }

        //binary for case sensitive
        $sql = "SELECT cons_id FROM " . $this->tableGateway->table . " WHERE constant_name = ? $case_sensitive_collation";

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
