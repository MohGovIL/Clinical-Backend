<?php

namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;

/**
 * Class PumpsTable
 * @package Pouring\Model
 */
class LangLanguagesTable
{

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
    public function getLangCode($id)
    {
        $rowset = $this->tableGateway->select(array('lang_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row->lang_code;
    }


    public function getLangCodeByGlobals()
    {
        $rowset = $this->tableGateway->select(array('lang_description' => $GLOBALS['language_default']));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row->lang_id;
    }

    public function getLangIdByGlobals()
    {
        $rowset = $this->tableGateway->select(array('lang_description' => $GLOBALS['language_default']));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row->lang_id;
    }

}
