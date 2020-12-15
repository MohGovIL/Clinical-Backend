<?php


namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;

class PrescriptionsTable
{
    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getPatientPrescription($eid,$pid){
        return $this->buildGenericSelect(['encounter'=>$eid,'patient_id'=>$pid]);
    }

}
