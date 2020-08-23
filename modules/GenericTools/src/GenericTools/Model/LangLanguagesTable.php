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
    /**
     * Returns language directionality as string 'rtl' or 'ltr'
     * @param int $lang_id language code
     * @return string 'ltr' 'rtl'
     */
    public function getLanguageDir($lang_id)
    {
        // validate language id
        $lang_id = empty($lang_id) ? 1 : $lang_id;

        $rowset = $this->tableGateway->select(array('lang_id' => $lang_id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return !empty($row->lang_is_rtl) ? 'rtl' : 'ltr';
    }


    public function getLanguageSettings()
    {
        $langId=(!is_null($_SESSION['language_choice'])) ? $_SESSION['language_choice'] : $this->getLangIdByGlobals();
        $langDir =(!is_null($_SESSION['language_direction'])) ? $_SESSION['language_direction'] : $this->getLanguageDir($langId);
        $langCode= $this->getLangCode($langId);
        $_SESSION['language_choice'] =(!is_null($_SESSION['language_choice'])) ? $_SESSION['language_direction'] : $langId;
        return array('langDir' => $langDir, 'langCode' => $langCode );

    }



}
