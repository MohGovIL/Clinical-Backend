<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 1/26/17
 * Time: 4:20 PM
 */

namespace ImportData\Model;


use Zend\Db\TableGateway\TableGateway;

class LangDefinitionsTable
{

    const HEBREW_ID  = 7;
    const ENGLISH_ID = 1;

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function insert($constId, $hebrew, $english){

        $this->tableGateway->insert(array('cons_id' => $constId, 'lang_id' => self::ENGLISH_ID, 'definition' => $english));
        $this->tableGateway->insert(array('cons_id' => $constId, 'lang_id' => self::HEBREW_ID, 'definition' => $hebrew));
    }

    public function update($constId, $hebrew, $english){

        $this->tableGateway->update(array('definition' => $english),array('cons_id' => $constId, 'lang_id' => self::ENGLISH_ID));
        $this->tableGateway->update(array( 'definition' => $hebrew),array('cons_id' => $constId, 'lang_id' => self::HEBREW_ID));
    }

}
