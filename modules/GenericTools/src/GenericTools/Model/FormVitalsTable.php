<?php


namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;

class FormVitalsTable
{
    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

}
