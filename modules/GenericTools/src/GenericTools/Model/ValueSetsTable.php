<?php


namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;

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
            "
            SELECT fvs.id as vs_id, fvs.title as vs_title, fvs.status as vs_status, fvss.system as system, lo.a_option_id as code, lo.a_title as display 
            FROM {$this->tableGateway->table} AS fvs 
            JOIN fhir_value_set_systems AS fvss ON fvs.id = fvss.vs_id 
            LEFT JOIN fhir_value_set_codes AS fvsc ON fvss.id = fvsc.vss_id 
            JOIN 
                (
                    select list_id as a_list_id, option_id as a_option_id, title as a_title, notes as a_notes, seq as a_seq, 'lists' as a_type
                    from list_options
                    union
                    select code_type as a_list_id, code as a_option_id, code_text as a_title, '' as a_notes, code as a_seq, 'codes' as a_type
                    from codes
                ) AS lo 
                ON 
                fvss.system = lo.a_list_id 
                AND 
                ( 
                    (fvss.type = 'All' AND lo.a_type = 'lists') 
                    OR 
                    (fvss.type = 'Partial' AND fvsc.code = lo.a_option_id AND lo.a_type = 'lists') 
                    OR 
                    (fvss.type = 'Exclude' AND fvsc.code != lo.a_option_id AND lo.a_type = 'lists') 
                    OR 
                    (fvss.type = 'Filter' AND fvss.filter = lo.a_notes AND lo.a_type = 'lists')
                    OR 
                    (fvss.type = 'Codes' AND lo.a_type = 'codes')
             
                ) 
                WHERE fvs.id = ? ORDER BY lo.a_list_id, lo.a_seq 
            ";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($id));
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results;
    }

}
