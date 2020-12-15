<?php

/**
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */

namespace FhirAPI\Model;

use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGateway;
use GenericTools;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

class FhirValidationSettingsTable
{
    protected $tableGateway;

    use GenericTools\Model\baseTable;
    use JoinBuilder;

    CONST FHIR_TYPE= "FHIR";
    CONST DB_TYPE= "DB";

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->join = array();
    }


    public function getActiveValidation($type,$fhirElmName=null)
    {
        if($type!==self::FHIR_TYPE && $type!==self::DB_TYPE){
            return false;
        }

        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();

        $where = new Where();
        $where->equalTo('type', $type);
        $where->AND->equalTo('active', 1);

        if(!is_null($fhirElmName)){
            $where->AND->equalTo('fhir_element', $fhirElmName);
        }
        $select->where($where);
        $rs = $this->tableGateway->selectWith($select);

        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }
        return $rsArray;
    }
}
