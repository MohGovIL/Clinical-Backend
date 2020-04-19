<?php


namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;

class ValueSetsTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /*
     * Get value set by the value set id
     */
    public function getValueSetById($id)
    {
        $sql=
            "SELECT fvs.id as vs_id, fvs.title as vs_title, fvs.status as vs_status, fvss.system as system, lo.option_id as code, lo.title as display " .
            "FROM {$this->tableGateway->table} AS fvs " .
            "JOIN fhir_value_set_systems AS fvss ON fvs.id = fvss.vs_id " .
            "LEFT JOIN fhir_value_set_codes AS fvsc ON fvss.id = fvsc.vss_id " .
            "JOIN list_options AS lo ON fvss.system = lo.list_id AND ( " .
                    "(fvss.type = 'All') " .
                    "OR " .
                    "(fvss.type = 'Partial' AND fvsc.code = lo.option_id) " .
                    "OR " .
                    "(fvss.type = 'Exclude' AND fvsc.code != lo.option_id) " .
                    "OR " .
                    "(fvss.type = 'Filter' AND fvss.filter = lo.notes) " .
                ") " .
            "WHERE fvs.id = ? " .
            "ORDER BY lo.list_id, lo.seq ";


        $statement = $this->tableGateway->adapter->createStatement($sql, array($id));
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results;
    }

}
