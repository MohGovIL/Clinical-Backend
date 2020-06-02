<?php

namespace ClinikalAPI\Model;

use Laminas\Db\Sql\Sql;

class TranslationTables
{

    public function __construct($dbAdapter)
    {

        $this->sql = new Sql($dbAdapter);
        $this->adapter = $dbAdapter;
    }


    /**
     * select users that belong to certain aro group
     * @param $aro_group
     * @return array
     */
    public function getAllTranslationByLangId($lid)
    {
        $sql = "SELECT c.constant_name, d.definition FROM lang_definitions as d
                JOIN lang_constants AS c ON d.cons_id = c.cons_id
                WHERE d.lang_id = ?";

        $statement = $this->adapter->createStatement($sql, array($lid));
        $return = $statement->execute();
        $results = array();
        foreach ($return as $row) {
            $results[$row['constant_name']] = $row['definition'];
        }
        return (isset($results)) ? $results : array();

    }


}
